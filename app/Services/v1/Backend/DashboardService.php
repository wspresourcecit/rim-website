<?php

namespace App\Services\v1\Backend;

use App\Models\Achievement;
use App\Models\AdmissionLead;
use App\Models\Authorization\Role;
use App\Models\Branch;
use App\Models\Course;
use App\Models\CourseDepartment;
use App\Models\CourseSoftware;
use App\Models\CourseType;
use App\Models\Facility;
use App\Models\Faq\Faq;
use App\Models\Faq\FaqCategory;
use App\Models\Feedback;
use App\Models\FreeClassLead;
use App\Models\FreeCourse;
use App\Models\FreeCourseLead;
use App\Models\Gallery\Gallery;
use App\Models\JobCircular;
use App\Models\JobDepartment;
use App\Models\LearningMode;
use App\Models\Mentor;
use App\Models\PartnerCompany;
use App\Models\PaymentMethod;
use App\Models\Seminar;
use App\Models\SeminarJoinLead;
use App\Models\SuccessStory;
use App\Models\SuccessStoryCategory;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class DashboardService
{
    /**
     * Module count definitions, keyed by the client-side metric key.
     * Each entry is [permission slug (without the `.access` suffix), model class].
     * A metric is only returned when the current admin holds `<slug>.access`.
     */
    private function modules(): array
    {
        return [
            'courses' => ['course', Course::class],
            'courseDepartments' => ['course.department', CourseDepartment::class],
            'courseTypes' => ['course.type', CourseType::class],
            'courseSoftwares' => ['course.software', CourseSoftware::class],
            'learningModes' => ['learning.mode', LearningMode::class],
            'freeCourses' => ['free-course', FreeCourse::class],
            'branches' => ['branch', Branch::class],
            'facilities' => ['facility', Facility::class],
            'mentors' => ['mentor', Mentor::class],
            'jobDepartments' => ['job.department', JobDepartment::class],
            'jobCirculars' => ['job.circular', JobCircular::class],
            'seminars' => ['seminar', Seminar::class],
            'partnerCompanies' => ['partner-company', PartnerCompany::class],
            'successStories' => ['success.story', SuccessStory::class],
            'successStoryCategories' => ['success.story.category', SuccessStoryCategory::class],
            'feedback' => ['feedback', Feedback::class],
            'achievements' => ['achievement', Achievement::class],
            'faqs' => ['faq', Faq::class],
            'faqCategories' => ['faq.category', FaqCategory::class],
            'galleries' => ['gallery', Gallery::class],
            'paymentMethods' => ['payment.method', PaymentMethod::class],
            'admissionLeads' => ['admission.lead', AdmissionLead::class],
            'freeClassLeads' => ['free.class.lead', FreeClassLead::class],
            'freeCourseLeads' => ['free.course.lead', FreeCourseLead::class],
            'admins' => ['admin', User::class],
            'roles' => ['role', Role::class],
        ];
    }

    /**
     * Lead models keyed by client-side key, with their permission slug.
     */
    private function leadModules(): array
    {
        return [
            'admission' => ['admission.lead', AdmissionLead::class],
            'freeClass' => ['free.class.lead', FreeClassLead::class],
            'freeCourse' => ['free.course.lead', FreeCourseLead::class],
            'seminarJoin' => ['seminar.join.lead', SeminarJoinLead::class],
        ];
    }

    /**
     * Build the dashboard payload, scoped to what the given admin may access.
     */
    public function overview(User $user): array
    {
        return [
            'counts' => $this->counts($user),
            'leads' => $this->leadTrends($user),
            'breakdowns' => $this->breakdowns($user),
            'recentAdmissionLeads' => $this->recentAdmissionLeads($user),
        ];
    }

    /**
     * Category breakdowns for the dashboard charts, each scoped by permission.
     * Every entry is a list of { label, value }, sorted by value desc.
     *
     * @return array<string, array<int, array{label:string,value:int}>>
     */
    private function breakdowns(User $user): array
    {
        $out = [];

        if ($user->check('course.access')) {
            $out['coursesByDepartment'] = $this->relationCounts(CourseDepartment::class, 'courses', 'name');
            $out['coursesByType'] = $this->relationCounts(CourseType::class, 'courses', 'name');
        }

        if ($user->check('mentor.access')) {
            $out['mentorsByDepartment'] = $this->relationCounts(CourseDepartment::class, 'mentors', 'name');
        }

        if ($user->check('job.circular.access')) {
            $out['circularsByDepartment'] = $this->relationCounts(JobDepartment::class, 'jobCirculars', 'name');
        }

        if ($user->check('admission.lead.access')) {
            $out['leadsByCourse'] = $this->groupCounts(AdmissionLead::class, 'course_id', 'course', 'title');
            $out['leadsByBranch'] = $this->groupCounts(AdmissionLead::class, 'branch_id', 'branch', 'name');
        }

        return array_filter($out, fn ($rows) => count($rows) > 0);
    }

    /**
     * Count a hasMany relation for every parent row: [{ label, value }, ...].
     */
    private function relationCounts(string $parentModel, string $relation, string $labelColumn): array
    {
        $countColumn = Str::snake($relation).'_count';

        return $parentModel::query()
            ->withCount($relation)
            ->orderByDesc($countColumn)
            ->get()
            ->map(fn ($row) => [
                'label' => (string) ($row->{$labelColumn} ?? '—'),
                'value' => (int) $row->{$countColumn},
            ])
            ->all();
    }

    /**
     * Group a model by a foreign key and resolve the related name: top 8 rows.
     */
    private function groupCounts(string $model, string $foreignKey, string $relation, string $labelColumn): array
    {
        return $model::query()
            ->selectRaw("{$foreignKey}, COUNT(*) as aggregate")
            ->groupBy($foreignKey)
            ->orderByDesc('aggregate')
            ->with("{$relation}:id,{$labelColumn}")
            ->take(8)
            ->get()
            ->map(fn ($row) => [
                'label' => (string) ($row->{$relation}->{$labelColumn} ?? 'Unassigned'),
                'value' => (int) $row->aggregate,
            ])
            ->all();
    }

    /**
     * Total row count per module the admin can access.
     *
     * @return array<string, int>
     */
    private function counts(User $user): array
    {
        $counts = [];

        foreach ($this->modules() as $key => [$slug, $model]) {
            if ($user->check("{$slug}.access")) {
                $counts[$key] = $model::query()->count();
            }
        }

        return $counts;
    }

    /**
     * Total / last-7-day / last-30-day submission counts per lead type.
     *
     * @return array<string, array{total:int,last7Days:int,last30Days:int}>
     */
    private function leadTrends(User $user): array
    {
        $now = Carbon::now();
        $trends = [];

        foreach ($this->leadModules() as $key => [$slug, $model]) {
            if (! $user->check("{$slug}.access")) {
                continue;
            }

            $trends[$key] = [
                'total' => $model::query()->count(),
                'last7Days' => $model::query()->where('created_at', '>=', $now->copy()->subDays(7))->count(),
                'last30Days' => $model::query()->where('created_at', '>=', $now->copy()->subDays(30))->count(),
            ];
        }

        return $trends;
    }

    /**
     * The six newest admission leads, or an empty list when not permitted.
     */
    private function recentAdmissionLeads(User $user): array
    {
        if (! $user->check('admission.lead.access')) {
            return [];
        }

        return AdmissionLead::with(['course:id,title', 'branch:id,name'])
            ->orderByDesc('id')
            ->take(6)
            ->get()
            ->map(fn (AdmissionLead $lead) => [
                'id' => $lead->id,
                'name' => $lead->name,
                'mobile' => $lead->mobile,
                'email' => $lead->email,
                'course' => $lead->course?->title,
                'branch' => $lead->branch?->name,
                'created_at' => $lead->created_at?->toDateTimeString(),
            ])
            ->all();
    }
}

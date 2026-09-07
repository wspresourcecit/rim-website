<?php

use App\Http\Controllers\v1\Backend\AboutController;
use App\Http\Controllers\v1\Backend\AchievementController;
use App\Http\Controllers\v1\Backend\AchievementPageController;
use App\Http\Controllers\v1\Backend\AuthController;
use App\Http\Controllers\v1\Backend\Authorization\AdminManageController;
use App\Http\Controllers\v1\Backend\Authorization\RoleManageController;
use App\Http\Controllers\v1\Backend\BannerController;
use App\Http\Controllers\v1\Backend\BonusClassCtaController;
use App\Http\Controllers\v1\Backend\BranchController;
use App\Http\Controllers\v1\Backend\CourseController;
use App\Http\Controllers\v1\Backend\CourseCareerController;
use App\Http\Controllers\v1\Backend\CourseModuleController;
use App\Http\Controllers\v1\Backend\CourseSoftwareController;
use App\Http\Controllers\v1\Backend\CourseSoftwareLinkController;
use App\Http\Controllers\v1\Backend\CourseDepartmentController;
use App\Http\Controllers\v1\Backend\CourseTypeController;
use App\Http\Controllers\v1\Backend\DashboardController;
use App\Http\Controllers\v1\Backend\CoursePriceController;
use App\Http\Controllers\v1\Backend\DiplomaCtaController;
use App\Http\Controllers\v1\Backend\BranchFacilityController;
use App\Http\Controllers\v1\Backend\Faq\FaqCategoryController;
use App\Http\Controllers\v1\Backend\Faq\FaqController;
use App\Http\Controllers\v1\Backend\Gallery\GalleryTypeController;
use App\Http\Controllers\v1\Backend\Gallery\GalleryController;
use App\Http\Controllers\v1\Backend\FacilityController;
use App\Http\Controllers\v1\Backend\FeedbackController;
use App\Http\Controllers\v1\Backend\FreeClassCtaController;
use App\Http\Controllers\v1\Backend\AdmissionLeadController;
use App\Http\Controllers\v1\Backend\SeminarJoinLeadController;
use App\Http\Controllers\v1\Frontend\SeminarController as FrontendSeminarController;
use App\Http\Controllers\v1\Backend\FreeClassLeadController;
use App\Http\Controllers\v1\Backend\FreeCourseLeadController;
use App\Http\Controllers\v1\Backend\FreeCourseController;
use App\Http\Controllers\v1\Backend\FreelancingController;
use App\Http\Controllers\v1\Backend\FacilityPageController;
use App\Http\Controllers\v1\Backend\PrivacyPolicyController;
use App\Http\Controllers\v1\Backend\JobCircularController;
use App\Http\Controllers\v1\Backend\JobDepartmentController;
use App\Http\Controllers\v1\Backend\CareerPlacementController;
use App\Http\Controllers\v1\Backend\JobPlacementController;
use App\Http\Controllers\v1\Backend\LearningModeController;
use App\Http\Controllers\v1\Backend\PageHeaderController;
use App\Http\Controllers\v1\Backend\MentorController;
use App\Http\Controllers\v1\Backend\PartnerCompanyController;
use App\Http\Controllers\v1\Backend\PaymentMethodController;
use App\Http\Controllers\v1\Backend\RoadmapController;
use App\Http\Controllers\v1\Backend\SectionHeaderController;
use App\Http\Controllers\v1\Backend\SeminarController;
use App\Http\Controllers\v1\Backend\SupportCtaController;
use App\Http\Controllers\v1\Backend\Setting\BackupController;
use App\Http\Controllers\v1\Backend\Setting\ExtensionController;
use App\Http\Controllers\v1\Backend\Setting\WebsiteAssetController;
use App\Http\Controllers\v1\Backend\StatisticController;
use App\Http\Controllers\v1\Backend\SuccessStoryCategoryController;
use App\Http\Controllers\v1\Backend\SuccessStoryController;
use App\Http\Controllers\v1\Backend\News\NewsCategoryController;
use App\Http\Controllers\v1\Backend\News\NewsController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('website-asset', [WebsiteAssetController::class, 'websiteAssets'])->middleware('throttle:public');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');

    // Public: lead capture from the admission modal.
    Route::post('admission-lead', [AdmissionLeadController::class, 'store'])->middleware(['throttle:public', 'verify.recaptcha']);

    // Public: lead capture from the "join a free seminar" modal.
    Route::post('seminar-join-lead', [FrontendSeminarController::class, 'storeJoinLead'])->middleware(['throttle:public', 'verify.recaptcha']);

    // Public: lead capture from the "join a free class" modal.
    Route::post('free-class-lead', [FreeClassLeadController::class, 'store'])->middleware(['throttle:public', 'verify.recaptcha']);

    // Public: lead capture from the "get a free course" modal.
    Route::post('free-course-lead', [FreeCourseLeadController::class, 'store'])->middleware(['throttle:public', 'verify.recaptcha']);

    Route::middleware(['auth:api', 'admin', 'throttle:api'])->group(function () {
        Route::get('check', [AuthController::class, 'check']);
        Route::get('permissions', [AuthController::class, 'permissions']);
        Route::post('logout', [AuthController::class, 'logout']);

        // Admin-panel dashboard: module counts + lead trends, scoped per admin's permissions.
        Route::get('dashboard', [DashboardController::class, 'index']);

        Route::apiResource('/role', RoleManageController::class)->middleware('permission:role');
        Route::get('roles/active', [RoleManageController::class, 'activeRoles']);
        Route::get('/all-permissions', [RoleManageController::class, 'groupWisePermissions']);

        Route::apiResource('admin', AdminManageController::class)->middleware('permission:admin');
        Route::post('admin/change-password', [AdminManageController::class, 'changePassword']);

        Route::apiResource('branch', BranchController::class)->middleware('permission:branch');
        Route::get('home/branches', [BranchController::class, 'homeBranches']);

        Route::prefix('banner')->middleware('permission:banner')->group(function () {
            Route::get('/', [BannerController::class, 'show']);
            Route::put('/', [BannerController::class, 'update']);
        });

        Route::prefix('statistic')->middleware('permission:statistic')->group(function () {
            Route::get('/', [StatisticController::class, 'index']);
            Route::put('/', [StatisticController::class, 'update']);
        });

        Route::prefix('diploma-cta')->middleware('permission:diploma-cta')->group(function () {
            Route::get('/', [DiplomaCtaController::class, 'show']);
            Route::put('/', [DiplomaCtaController::class, 'update']);
        });

        Route::prefix('support-cta')->middleware('permission:support-cta')->group(function () {
            Route::get('/', [SupportCtaController::class, 'show']);
            Route::put('/', [SupportCtaController::class, 'update']);
        });

        Route::prefix('section-header')->middleware('permission:section-header')->group(function () {
            Route::get('/', [SectionHeaderController::class, 'show']);
            Route::put('/', [SectionHeaderController::class, 'update']);
        });

        Route::prefix('page-header')->middleware('permission:page-header')->group(function () {
            Route::get('/', [PageHeaderController::class, 'show']);
            Route::put('/', [PageHeaderController::class, 'update']);
        });

        Route::apiResource('roadmap', RoadmapController::class)->middleware('permission:roadmap');

        Route::apiResource('mentor', MentorController::class)->middleware('permission:mentor');

        Route::apiResource('course-department', CourseDepartmentController::class)->middleware('permission:course.department');
        Route::match(['put', 'patch'], 'course/{course}/meta', [CourseController::class, 'updateMeta'])->middleware('permission:course');
        Route::apiResource('course', CourseController::class)->middleware('permission:course');

        Route::prefix('course-price')->group(function () {
            Route::get('/', [CoursePriceController::class, 'index']);
            Route::put('/', [CoursePriceController::class, 'update']);
        });

        Route::prefix('course-career')->group(function () {
            Route::get('/', [CourseCareerController::class, 'index']);
            Route::put('/', [CourseCareerController::class, 'update']);
        });

        Route::post('course-module/import', [CourseModuleController::class, 'import']);
        Route::apiResource('course-module', CourseModuleController::class);

        Route::prefix('course-software-link')->group(function () {
            Route::get('/', [CourseSoftwareLinkController::class, 'index']);
            Route::put('/', [CourseSoftwareLinkController::class, 'update']);
        });

        Route::prefix('free-class-cta')->middleware('permission:free.class.cta')->group(function () {
            Route::get('/', [FreeClassCtaController::class, 'index']);
            Route::put('/', [FreeClassCtaController::class, 'update']);
        });

        Route::prefix('bonus-class-cta')->middleware('permission:bonus.class.cta')->group(function () {
            Route::get('/', [BonusClassCtaController::class, 'index']);
            Route::put('/', [BonusClassCtaController::class, 'update']);
        });

        Route::apiResource('course-type', CourseTypeController::class)->middleware('permission:course.type');

        Route::apiResource('course-software', CourseSoftwareController::class)->middleware('permission:course.software');
        Route::get('course-softwares/active', [CourseSoftwareController::class, 'activeCourseSoftwares']);

        Route::apiResource('learning-mode', LearningModeController::class)->middleware('permission:learning.mode');

        Route::apiResource('facility', FacilityController::class)->middleware('permission:facility');
        Route::get('facilities/active', [FacilityController::class, 'activeFacilities']);

        Route::prefix('branch-facility')->middleware('permission:facility')->group(function () {
            Route::get('/', [BranchFacilityController::class, 'index']);
            Route::put('/', [BranchFacilityController::class, 'update']);
        });

        Route::prefix('job-placement')->middleware('permission:job-placement')->group(function () {
            Route::get('/', [JobPlacementController::class, 'show']);
            Route::put('/', [JobPlacementController::class, 'update']);
        });

        Route::prefix('career-placement')->middleware('permission:career-placement')->group(function () {
            Route::get('/', [CareerPlacementController::class, 'show']);
            Route::put('/', [CareerPlacementController::class, 'update']);
        });

        Route::prefix('freelancing')->middleware('permission:freelancing')->group(function () {
            Route::get('/', [FreelancingController::class, 'show']);
            Route::put('/', [FreelancingController::class, 'update']);
        });

        Route::apiResource('facility-page', FacilityPageController::class)->middleware('permission:facility-page');

        Route::apiResource('privacy-policy', PrivacyPolicyController::class)->middleware('permission:privacy-policy');

        Route::prefix('about')->middleware('permission:about')->group(function () {
            Route::get('/', [AboutController::class, 'show']);
            Route::put('/', [AboutController::class, 'update']);
        });

        Route::prefix('achievement-page')->middleware('permission:achievement-page')->group(function () {
            Route::get('/', [AchievementPageController::class, 'show']);
            Route::put('/', [AchievementPageController::class, 'update']);
        });

        Route::get('job-departments/active', [JobDepartmentController::class, 'activeJobDepartments']);
        Route::apiResource('job-department', JobDepartmentController::class)->middleware('permission:job.department');
        Route::apiResource('job-circular', JobCircularController::class)->middleware('permission:job.circular');

        Route::get('seminars/active', [SeminarController::class, 'activeSeminars']);
        Route::apiResource('seminar', SeminarController::class)->middleware('permission:seminar');

        Route::apiResource('payment-method', PaymentMethodController::class)->middleware('permission:payment.method');

        Route::apiResource('admission-lead', AdmissionLeadController::class)
            ->only(['index', 'show', 'update', 'destroy'])
            ->middleware('permission:admission.lead');

        Route::apiResource('seminar-join-lead', SeminarJoinLeadController::class)
            ->only(['index', 'show', 'update', 'destroy'])
            ->middleware('permission:seminar.join.lead');

        Route::apiResource('free-class-lead', FreeClassLeadController::class)
            ->only(['index', 'show', 'update', 'destroy'])
            ->middleware('permission:free.class.lead');

        Route::apiResource('free-course-lead', FreeCourseLeadController::class)
            ->only(['index', 'show', 'update', 'destroy'])
            ->middleware('permission:free.course.lead');

        Route::prefix('setting')->group(function () {
            Route::get('website-asset', [WebsiteAssetController::class, 'index'])->middleware('permission:website.asset');
            Route::post('website-asset', [WebsiteAssetController::class, 'store'])->middleware('permission:website.asset');
            Route::apiResource('extension', ExtensionController::class)->except('store', 'destroy')->middleware('permission:extension');
        });
        Route::get('backup', [BackupController::class, 'index'])->middleware('permission:backup');
        Route::get('backup/generate', [BackupController::class, 'create'])->middleware('permission:backup');
        Route::post('backup/download/{fileName}', [BackupController::class, 'download'])->middleware('permission:backup');
        Route::get('backup/delete/{fileName}', [BackupController::class, 'destroy'])->middleware('permission:backup');
        Route::post('backup/cloud', [BackupController::class, 'couldBackup'])->middleware('permission:backup');

        Route::apiResource('success-story-category', SuccessStoryCategoryController::class)->middleware('permission:success.story.category');
        Route::apiResource('success-story', SuccessStoryController::class)->middleware('permission:success.story');

        Route::apiResource('news-category', NewsCategoryController::class)->middleware('permission:news.category');
        Route::apiResource('news', NewsController::class)->middleware('permission:news');
        Route::get('news-categories/active', [NewsCategoryController::class, 'activeCategories']);

        Route::apiResource('feedback', FeedbackController::class)->middleware('permission:feedback');
        Route::get('feedbacks/active', [FeedbackController::class, 'activeFeedbacks']);

        Route::apiResource('achievement', AchievementController::class)->middleware('permission:achievement');
        Route::get('achievements/active', [AchievementController::class, 'activeAchievements']);

        Route::apiResource('partner-company', PartnerCompanyController::class)->middleware('permission:partner-company');
        Route::get('partner-companies/active', [PartnerCompanyController::class, 'activePartnerCompanies']);

        Route::apiResource('free-course', FreeCourseController::class)->middleware('permission:free-course');
        Route::get('free-courses/active', [FreeCourseController::class, 'activeFreeCourses']);

        Route::apiResource('faq-category', FaqCategoryController::class)->middleware('permission:faq.category');
        Route::apiResource('faq', FaqController::class)->middleware('permission:faq');
        Route::get('faq-categories/active', [FaqCategoryController::class, 'activeCategories']);

        Route::apiResource('gallery-type', GalleryTypeController::class)->middleware('permission:gallery.type');
        Route::apiResource('gallery', GalleryController::class)->middleware('permission:gallery');
        Route::get('gallery-types/active', [GalleryTypeController::class, 'activeTypes']);

        Route::get('branches/active', [BranchController::class, 'activeBranches']);
        Route::get('course-departments/active', [CourseDepartmentController::class, 'activeCourseDepartments']);
        Route::get('courses/active', [CourseController::class, 'activeCourses']);
        Route::get('success-story-categories/active', [SuccessStoryCategoryController::class, 'activeCategories']);
        Route::get('learning-modes/active', [LearningModeController::class, 'activeLearningModes']);
        Route::get('course-types/active', [CourseTypeController::class, 'activeCourseTypes']);
    });
});

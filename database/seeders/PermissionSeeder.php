<?php

namespace Database\Seeders;

use App\Models\Authorization\Permission;
use App\Models\Authorization\PermissionGroup;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Role Manage
        $rolesManage = PermissionGroup::updateOrCreate([
            'name' => 'Roles Manage',
        ]);

        Permission::updateOrCreate([
            'permission_group_id' => $rolesManage->id,
            'name' => 'Access Roles',
            'slug' => 'role.access',
        ]);

        Permission::updateOrCreate([
            'permission_group_id' => $rolesManage->id,
            'name' => 'Create Roles',
            'slug' => 'role.create',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $rolesManage->id,
            'name' => 'Edit Roles',
            'slug' => 'role.edit',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $rolesManage->id,
            'name' => 'Destroy Roles',
            'slug' => 'role.destroy',
        ]);

        // Website Asset
        $websiteAsset = PermissionGroup::updateOrCreate([
            'name' => 'Website Asset Manage',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $websiteAsset->id,
            'name' => 'Access Website Asset',
            'slug' => 'website.asset.access',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $websiteAsset->id,
            'name' => 'Create Website Asset',
            'slug' => 'website.asset.create',
        ]);

        // Extension
        $extensionManage = PermissionGroup::updateOrCreate([
            'name' => 'Extension Manage',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $extensionManage->id,
            'name' => 'Access Extension',
            'slug' => 'extension.access',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $extensionManage->id,
            'name' => 'Create Extension',
            'slug' => 'extension.create',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $extensionManage->id,
            'name' => 'Edit Extension',
            'slug' => 'extension.edit',
        ]);

        // Backup
        $backup = PermissionGroup::updateOrCreate([
            'name' => 'Backup Manage',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $backup->id,
            'name' => 'Access Backup',
            'slug' => 'backup.access',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $backup->id,
            'name' => 'Create Backup',
            'slug' => 'backup.create',
        ]);

        // Admin Manage
        $admin = PermissionGroup::updateOrCreate([
            'name' => 'Admin Manage',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $admin->id,
            'name' => 'Access Admin',
            'slug' => 'admin.access',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $admin->id,
            'name' => 'Create Admin',
            'slug' => 'admin.create',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $admin->id,
            'name' => 'Edit Admin',
            'slug' => 'admin.edit',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $admin->id,
            'name' => 'Destroy Admin',
            'slug' => 'admin.destroy',
        ]);


        // Privacy Policy Manage
        $privacyPolicy = PermissionGroup::updateOrCreate([
            'name' => 'Privacy Policy Manage',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $privacyPolicy->id,
            'name' => 'Access Privacy Policy',
            'slug' => 'privacy-policy.access',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $privacyPolicy->id,
            'name' => 'Create Privacy Policy',
            'slug' => 'privacy-policy.create',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $privacyPolicy->id,
            'name' => 'Edit Privacy Policy',
            'slug' => 'privacy-policy.edit',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $privacyPolicy->id,
            'name' => 'Destroy Privacy Policy',
            'slug' => 'privacy-policy.destroy',
        ]);


        // News Category
        $newsCategory = PermissionGroup::updateOrCreate([
            'name' => 'News Category Manage',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $newsCategory->id,
            'name' => 'Access News Category',
            'slug' => 'news.category.access',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $newsCategory->id,
            'name' => 'Create News Category',
            'slug' => 'news.category.create',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $newsCategory->id,
            'name' => 'Edit News Category',
            'slug' => 'news.category.edit',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $newsCategory->id,
            'name' => 'Destroy News Category',
            'slug' => 'news.category.destroy',
        ]);

        // News
        $news = PermissionGroup::updateOrCreate([
            'name' => 'News Manage',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $news->id,
            'name' => 'Access News',
            'slug' => 'news.access',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $news->id,
            'name' => 'Create News',
            'slug' => 'news.create',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $news->id,
            'name' => 'Edit News',
            'slug' => 'news.edit',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $news->id,
            'name' => 'Destroy News',
            'slug' => 'news.destroy',
        ]);


        // FAQ Category
        $faqCategory = PermissionGroup::updateOrCreate([
            'name' => 'FAQ Category Manage',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $faqCategory->id,
            'name' => 'Access FAQ Category',
            'slug' => 'faq.category.access',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $faqCategory->id,
            'name' => 'Create FAQ Category',
            'slug' => 'faq.category.create',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $faqCategory->id,
            'name' => 'Edit FAQ Category',
            'slug' => 'faq.category.edit',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $faqCategory->id,
            'name' => 'Destroy FAQ Category',
            'slug' => 'faq.category.destroy',
        ]);

        // FAQ
        $faq = PermissionGroup::updateOrCreate([
            'name' => 'FAQ Manage',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $faq->id,
            'name' => 'Access FAQ',
            'slug' => 'faq.access',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $faq->id,
            'name' => 'Create FAQ',
            'slug' => 'faq.create',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $faq->id,
            'name' => 'Edit FAQ',
            'slug' => 'faq.edit',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $faq->id,
            'name' => 'Destroy FAQ',
            'slug' => 'faq.destroy',
        ]);

        // Gallery Type
        $galleryType = PermissionGroup::updateOrCreate([
            'name' => 'Gallery Type Manage',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $galleryType->id,
            'name' => 'Access Gallery Type',
            'slug' => 'gallery.type.access',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $galleryType->id,
            'name' => 'Create Gallery Type',
            'slug' => 'gallery.type.create',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $galleryType->id,
            'name' => 'Edit Gallery Type',
            'slug' => 'gallery.type.edit',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $galleryType->id,
            'name' => 'Destroy Gallery Type',
            'slug' => 'gallery.type.destroy',
        ]);

        // Gallery
        $gallery = PermissionGroup::updateOrCreate([
            'name' => 'Gallery Manage',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $gallery->id,
            'name' => 'Access Gallery',
            'slug' => 'gallery.access',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $gallery->id,
            'name' => 'Create Gallery',
            'slug' => 'gallery.create',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $gallery->id,
            'name' => 'Edit Gallery',
            'slug' => 'gallery.edit',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $gallery->id,
            'name' => 'Destroy Gallery',
            'slug' => 'gallery.destroy',
        ]);

        // Partner Company
        $partnerCompany = PermissionGroup::updateOrCreate([
            'name' => 'Partner Company Manage',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $partnerCompany->id,
            'name' => 'Access Partner Company',
            'slug' => 'partner-company.access',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $partnerCompany->id,
            'name' => 'Create Partner Company',
            'slug' => 'partner-company.create',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $partnerCompany->id,
            'name' => 'Edit Partner Company',
            'slug' => 'partner-company.edit',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $partnerCompany->id,
            'name' => 'Destroy Partner Company',
            'slug' => 'partner-company.destroy',
        ]);

        // Page Header Manage
        $pageHeader = PermissionGroup::updateOrCreate([
            'name' => 'Page Header Manage',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $pageHeader->id,
            'name' => 'Access Page Header',
            'slug' => 'page-header.access',
        ]);
        Permission::updateOrCreate([
            'permission_group_id' => $pageHeader->id,
            'name' => 'Edit Page Header',
            'slug' => 'page-header.edit',
        ]);
    }
}

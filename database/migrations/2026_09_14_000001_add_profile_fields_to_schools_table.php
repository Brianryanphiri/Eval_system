<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->string('favicon_path')->nullable()->after('logo_path');
            $table->string('motto')->nullable()->after('name');
            $table->string('principal_name')->nullable()->after('motto');
            $table->string('principal_signature_path')->nullable()->after('principal_name');

            $table->string('website')->nullable()->after('email');
            $table->text('email_signature')->nullable()->after('website');
            $table->string('registration_number')->nullable()->after('email_signature');
            $table->unsignedSmallInteger('founded_year')->nullable()->after('registration_number');

            $table->string('primary_color', 7)->default('#0F0F0F')->after('founded_year');
            $table->string('accent_color', 7)->default('#7AC142')->after('primary_color');

            $table->string('phone_secondary')->nullable()->after('phone');

            $table->string('address_street')->nullable()->after('address');
            $table->string('address_city')->nullable()->after('address_street');
            $table->string('address_province')->nullable()->after('address_city');
            $table->string('address_postal_code')->nullable()->after('address_province');

            $table->string('timezone')->default('Africa/Blantyre')->after('country');
            $table->string('currency', 3)->default('MWK')->after('timezone');
            $table->string('academic_system')->default('secondary')->after('institution_type');

            $table->boolean('public_portal_enabled')->default(false)->after('setup_completed');
        });
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn([
                'favicon_path',
                'motto',
                'principal_name',
                'principal_signature_path',
                'website',
                'email_signature',
                'registration_number',
                'founded_year',
                'primary_color',
                'accent_color',
                'phone_secondary',
                'address_street',
                'address_city',
                'address_province',
                'address_postal_code',
                'timezone',
                'currency',
                'academic_system',
                'public_portal_enabled',
            ]);
        });
    }
};
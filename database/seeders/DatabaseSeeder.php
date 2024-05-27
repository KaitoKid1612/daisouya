<?php

namespace Database\Seeders;

use App\Models\DriverPlan;
use App\Models\DriverTaskPaymentStatus;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call([
            GenderSeeder::class,
            RegionSeeder::class,
            PrefectureSeeder::class,
            UserTypeSeeder::class,
            DeliveryOfficeChargeUserTypeSeeder::class,
            DeliveryOfficeTypeSeeder::class,
            DriverPlanSeeder::class,
            DriverEntryStatusSeeder::class,
            DriverSeeder::class,
            AdminPermissionGroupSeeder::class,
            AdminSeeder::class,
            DeliveryCompanySeeder::class,
            DeliveryOfficeSeeder::class,
            DeliveryPickupAddrSeeder::class,
            DriverRegisterDeliveryOfficeSeeder::class,
            DriverRegisterDeliveryOfficeMemoSeeder::class,
            DriverTaskReviewPublicStatusSeeder::class,
            DriverTaskStatusSeeder::class,
            DriverTaskPaymentStatusSeeder::class,
            DriverTaskRefundStatusSeeder::class,
            DriverTaskPlanSeeder::class,
            DriverTaskSeeder::class,
            DriverTaskPlanAllowDriverSeeder::class,
            DriverTaskReviewSeeder::class,
            DriverSchedulesSeeder::class,

            DriverEntryStatusSeeder::class,
            WebConfigBaseSeeder::class,
            WebConfigSystemSeeder::class,
            WebLogLevelSeeder::class,
            WebNoticeTypeSeeder::class,

            WebContactTypeSeeder::class,
            WebContactStatusSeeder::class,
            WebContactSeeder::class,
            RegisterRequestStatusSeeder::class,
            RegisterRequestDeliveryOfficeSeeder::class,
            RegisterRequestDriverSeeder::class,
            DeliveryOfficeTaskReviewPublicStatusSeeder::class,
            DeliveryOfficeTaskReviewSeeder::class,

            WebPaymentLogStatusSeeder::class,
            WebPaymentReasonSeeder::class,
            WebPaymentLogSeeder::class,

            FcmDeviceTokenDeliveryOfficeSeeder::class,
            FcmDeviceTokenDriverSeeder::class,

            WebBusySeasonSeeder::class,
        ]);
    }
}

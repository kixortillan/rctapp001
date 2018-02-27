<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DashboardModuleTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $services = DB::table('tbl_services')
            ->where('is_deleted', false)
            ->get();
        $loanBenefitTypes = DB::table('tbl_loan_benefits_type')
            ->orderBy('id', 'asc')
            ->get();

        for ($i = 0; $i < 300; $i++) {
            //seed dummy data
            //for each user create a tbl_mobile_user_logs record
            DB::table('tbl_mobile_users')
                ->orderBy('id', 'asc')->chunk(50,
                function ($mobUsers)
                 use ($services, $loanBenefitTypes) {

                    $svcLoanAndBeneApp = $services->where('service', 'Loans and Benefits Application')->first();

                    $mobUsers->each(function ($user)
                         use ($loanBenefitTypes, $svcLoanAndBeneApp) {
                            $type = random_int($loanBenefitTypes->first()->id, $loanBenefitTypes->last()->id);

                            $faker = \Faker\Factory::create();
                            $dt = Carbon::instance($faker->dateTime());
                            $dt->year(random_int(Carbon::today()->year - 5, Carbon::today()->year));

                            DB::table('tbl_mobile_user_logs')
                                ->insert([
                                    'mobile_user_id' => $user->id,
                                    'loan_benefit_type_id' => $type,
                                    'service_id' => $svcLoanAndBeneApp->id,
                                    'date_created' => $dt,
                                ]);
                        });
                });

            DB::table('tbl_mobile_users')
                ->orderBy('id', 'asc')->chunk(50,
                function ($mobUsers)
                 use ($services, $loanBenefitTypes) {

                    $svcLoanAndBeneApp = $services->where('service', 'Loans and Benefits Application')->first();

                    $mobUsers->each(function ($user)
                         use ($loanBenefitTypes, $services, $svcLoanAndBeneApp) {

                            do {
                                $svcId = random_int($services->first()->id, $services->last()->id);
                            } while ($svcId == $svcLoanAndBeneApp->id);

                            $faker = \Faker\Factory::create();
                            $dt = Carbon::instance($faker->dateTime());
                            $dt->year(random_int(Carbon::today()->year - 5, Carbon::today()->year));

                            DB::table('tbl_mobile_user_logs')
                                ->insert([
                                    'mobile_user_id' => $user->id,
                                    'loan_benefit_type_id' => null,
                                    'service_id' => $svcId,
                                    'date_created' => $dt,
                                ]);
                        });
                });
        }
    }
}

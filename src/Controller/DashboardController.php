<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Dashboard Klinik: ringkasan kunjungan rawat jalan hari ini.
 */
class DashboardController extends AppController
{
    public function index()
    {
        $today = date('Y-m-d');

        $Registrations = $this->fetchTable('Registrations');
        $Visits = $this->fetchTable('OutpatientVisits');
        $Patients = $this->fetchTable('Patients');
        $Billings = $this->fetchTable('OutpatientBillings');

        $totalPatients = $Patients->find()->count();

        $registrationsToday = $Registrations->find()
            ->where(['Registrations.registration_date' => $today, 'Registrations.service_type' => 'rawat_jalan'])
            ->count();

        $visitStatus = ['waiting' => 0, 'called' => 0, 'in_progress' => 0, 'done' => 0];
        $rows = $Visits->find()
            ->select(['status', 'total' => $Visits->find()->func()->count('*')])
            ->where(['OutpatientVisits.visit_date' => $today])
            ->groupBy(['status'])
            ->enableHydration(false)
            ->toArray();
        foreach ($rows as $r) {
            $visitStatus[(string)$r['status']] = (int)$r['total'];
        }

        $waitingPayment = $Billings->find()
            ->where(['OutpatientBillings.status' => 'waiting_payment'])
            ->count();

        $todayVisits = $Visits->find()
            ->contain(['Patients', 'Doctors'])
            ->where(['OutpatientVisits.visit_date' => $today])
            ->orderBy(['OutpatientVisits.queue_number' => 'ASC'])
            ->limit(20)
            ->all();

        $this->set(compact(
            'today',
            'totalPatients',
            'registrationsToday',
            'visitStatus',
            'waitingPayment',
            'todayVisits'
        ));
    }
}

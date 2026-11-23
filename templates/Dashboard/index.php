<?php
/**
 * @var \App\View\AppView $this
 */
$labels = [
    'waiting' => 'Menunggu',
    'called' => 'Dipanggil',
    'in_progress' => 'Sedang Diperiksa',
    'done' => 'Selesai',
];
?>
<h1 class="page-header">Dashboard <small><?= h(date('d-m-Y', strtotime($today))) ?></small></h1>

<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="widget widget-stats bg-blue">
            <div class="stats-icon"><i class="fa fa-users"></i></div>
            <div class="stats-info"><h4>TOTAL PASIEN</h4><p><?= number_format((int)$totalPatients, 0, ',', '.') ?></p></div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="widget widget-stats bg-info">
            <div class="stats-icon"><i class="fa fa-clipboard-list"></i></div>
            <div class="stats-info"><h4>REGISTRASI HARI INI</h4><p><?= number_format((int)$registrationsToday, 0, ',', '.') ?></p></div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="widget widget-stats bg-orange">
            <div class="stats-icon"><i class="fa fa-hourglass-half"></i></div>
            <div class="stats-info"><h4>MENUNGGU DIPERIKSA</h4><p><?= (int)$visitStatus['waiting'] ?></p></div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="widget widget-stats bg-red">
            <div class="stats-icon"><i class="fa fa-cash-register"></i></div>
            <div class="stats-info"><h4>MENUNGGU PEMBAYARAN</h4><p><?= (int)$waitingPayment ?></p></div>
        </div>
    </div>
</div>

<div class="panel panel-inverse">
    <div class="panel-heading"><h4 class="panel-title">Kunjungan Rawat Jalan Hari Ini</h4></div>
    <div class="panel-body">
        <p>
            <?php foreach ($labels as $key => $label): ?>
                <span class="badge badge-secondary mr-2"><?= h($label) ?>: <?= (int)$visitStatus[$key] ?></span>
            <?php endforeach; ?>
        </p>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr><th>No. Antrian</th><th>No. Registrasi</th><th>Pasien</th><th>Poli</th><th>Dokter</th><th>Status</th></tr>
                </thead>
                <tbody>
                <?php foreach ($todayVisits as $v): ?>
                    <tr>
                        <td><?= h($v->queue_number) ?></td>
                        <td><?= h($v->registration_no) ?></td>
                        <td><?= h($v->patient->full_name ?? '-') ?></td>
                        <td><?= h($v->polyclinic_name) ?></td>
                        <td><?= h($v->doctor->doctor_name ?? '-') ?></td>
                        <td><?= h($labels[$v->status] ?? $v->status) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($todayVisits->isEmpty()): ?>
                    <tr><td colspan="6" class="text-center text-muted">Belum ada kunjungan hari ini.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

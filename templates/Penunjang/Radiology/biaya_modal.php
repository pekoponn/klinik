<?php
/**
 * Simpan di: templates/element/Penunjang/Radiology/biaya_modal.php
 * Dipanggil dari index.php:  <?= $this->element('Penunjang/Radiology/biaya_modal') ?>
 */
$urlBiaya = $this->Url->build(['action' => 'biaya']);
$urlReg = $this->Url->build(['action' => 'searchRegistration']);
?>
<style>
    #modalBiaya .modal-header { background:#242a30; color:#fff; }
    #modalBiaya .modal-header .close { color:#fff; opacity:.8; }
    #modalBiaya .sec-title { background:#242a30; color:#fff; padding:4px 10px; font-weight:600; font-size:13px; }
    #modalBiaya .tbl-scroll { max-height:170px; overflow-y:auto; border:1px solid #dee2e6; }
    #modalBiaya .tbl-scroll thead th { position:sticky; top:0; background:#343a40; color:#fff; z-index:1; }
    #modalBiaya .form-control[readonly] { background:#f1f3f5; }
    #modalBiaya label { margin-bottom:2px; font-size:12px; font-weight:600; }
    #modalBiaya .reg-results { position:absolute; z-index:1060; left:0; right:0; top:100%; max-height:220px; overflow-y:auto; background:#fff; border:1px solid #ced4da; display:none; }
    #modalBiaya .reg-results a { display:block; padding:6px 10px; border-bottom:1px solid #eee; color:#212529; cursor:pointer; }
    #modalBiaya .reg-results a:hover { background:#e9ecef; }
    #modalBiaya .num { text-align:right; }
</style>

<div class="modal fade" id="modalBiaya" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document" style="max-width:1150px;">
    <div class="modal-content">
      <div class="modal-header py-2">
        <h5 class="modal-title"><i class="fa fa-arrow-circle-right"></i> Informasi Biaya Pasien</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <div class="modal-body" id="biayaBody">

        <!-- ===== HEADER PASIEN ===== -->
        <div class="form-row">
          <div class="col-md-4 mb-2">
            <label>No Registrasi</label>
            <div class="input-group input-group-sm position-relative">
              <input type="text" id="bc-reg" class="form-control" placeholder="No Reg / Nama / No RM" autocomplete="off">
              <div class="input-group-append">
                <button type="button" class="btn btn-warning" id="bc-search"><i class="fa fa-search"></i></button>
              </div>
              <div class="reg-results" id="bc-results"></div>
            </div>
          </div>
          <div class="col-md-2 mb-2"><label>Rek Medis</label><input type="text" id="bc-rm" class="form-control form-control-sm" readonly></div>
          <div class="col-md-2 mb-2"><label>Tgl Masuk</label><input type="text" id="bc-tglmasuk" class="form-control form-control-sm" readonly></div>
          <div class="col-md-1 mb-2"><label>Jam</label><input type="text" id="bc-jammasuk" class="form-control form-control-sm" readonly></div>
          <div class="col-md-2 mb-2"><label>Sampai Tgl</label><input type="date" id="bc-sampai" class="form-control form-control-sm"></div>
          <div class="col-md-1 mb-2 d-flex align-items-end">
            <button type="button" class="btn btn-success btn-sm btn-block" id="bc-proses"><i class="fa fa-play"></i> Proses</button>
          </div>
        </div>
        <div class="form-row">
          <div class="col-md-8 mb-2"><label>Nama Pasien</label><input type="text" id="bc-nama" class="form-control form-control-sm" readonly></div>
          <div class="col-md-4 mb-2 d-flex align-items-end">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" id="bc-tutup">
              <label class="custom-control-label" for="bc-tutup">TUTUP SEMENTARA</label>
            </div>
          </div>
        </div>

        <!-- ===== PEMAKAIAN KAMAR ===== -->
        <div class="sec-title">Pemakaian Kamar</div>
        <div class="tbl-scroll mb-1">
          <table class="table table-bordered table-sm mb-0">
            <thead><tr>
              <th>Ruang</th><th>Kelas</th><th>Tgl Masuk</th><th>Sampai Tgl</th>
              <th class="num">Tarif/Hari</th><th class="num">Hari</th><th class="num">Total Biaya Rp</th>
            </tr></thead>
            <tbody id="bc-kamar"><tr><td colspan="7" class="text-center text-muted">-</td></tr></tbody>
          </table>
        </div>
        <div class="text-right mb-2">
          <button type="button" class="btn btn-xs btn-info" id="bc-cek"><i class="fa fa-sync"></i> CEK</button>
          <button type="button" class="btn btn-xs btn-default" disabled>cek paket</button>
        </div>

        <!-- ===== TINDAKAN ===== -->
        <div class="sec-title">Tindakan dan Pengobatan Selama Perawatan</div>
        <div class="tbl-scroll mb-2" style="max-height:200px;">
          <table class="table table-bordered table-sm mb-0">
            <thead><tr>
              <th style="width:170px;">Nomor Transaksi</th><th>Tindakan dan Obat/Obatan</th>
              <th style="width:110px;">Tanggal</th><th class="num" style="width:140px;">Total Biaya Rp</th>
            </tr></thead>
            <tbody id="bc-tindakan"><tr><td colspan="4" class="text-center text-muted">-</td></tr></tbody>
          </table>
        </div>

        <div class="row">
          <!-- checkbox totalnya saja -->
          <div class="col-md-4 mb-2">
            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input bc-only" data-cat="ri" id="bc-o-ri"><label class="custom-control-label" for="bc-o-ri">R/I Totalnya Saja</label></div>
            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input bc-only" data-cat="bedah" id="bc-o-bedah"><label class="custom-control-label" for="bc-o-bedah">Bedah Totalnya Saja</label></div>
            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input bc-only" data-cat="ro" id="bc-o-ro"><label class="custom-control-label" for="bc-o-ro">RO Totalnya Saja</label></div>
            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input bc-only" data-cat="lab" id="bc-o-lab"><label class="custom-control-label" for="bc-o-lab">LAB Totalnya Saja</label></div>
            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input bc-only" data-cat="resep" id="bc-o-resep"><label class="custom-control-label" for="bc-o-resep">Resep Totalnya Saja</label></div>
          </div>

          <!-- total -->
          <div class="col-md-8 mb-2">
            <div class="form-row align-items-center mb-1">
              <div class="col-5 text-right"><label>Total Keseluruhan Biaya Kamar dan Perawatan Rp</label></div>
              <div class="col-4 offset-3"><input type="text" id="bc-total" class="form-control form-control-sm num" readonly value="0"></div>
            </div>
            <div class="form-row align-items-center mb-1">
              <div class="col-3 text-right"><label>Administrasi %</label></div>
              <div class="col-2"><input type="number" step="0.01" id="bc-adm-pct" class="form-control form-control-sm num" value="0"></div>
              <div class="col-1 text-right"><label>Rp</label></div>
              <div class="col-3"><input type="number" step="1" id="bc-adm-rp" class="form-control form-control-sm num" value="0"></div>
              <div class="col-1 text-right"><label>Materai</label></div>
              <div class="col-2"><input type="number" step="1" id="bc-materai" class="form-control form-control-sm num" value="0"></div>
            </div>
            <div class="form-row align-items-center mb-1">
              <div class="col-3 text-right"><label>Subsidi %</label></div>
              <div class="col-2"><input type="number" step="0.01" id="bc-sub-pct" class="form-control form-control-sm num" value="0"></div>
              <div class="col-1 text-right"><label>Rp</label></div>
              <div class="col-3"><input type="number" step="1" id="bc-sub-rp" class="form-control form-control-sm num" value="0"></div>
            </div>
            <div class="form-row align-items-center">
              <div class="col-5 text-right"><label>Total Tagihan Sebelum Dikurangi Uang Muka</label></div>
              <div class="col-4 offset-3"><input type="text" id="bc-before" class="form-control form-control-sm num font-weight-bold" readonly value="0"></div>
            </div>
          </div>
        </div>

        <!-- ===== UANG MUKA ===== -->
        <div class="sec-title">Uang Muka dan Pembayaran yang Sudah Dilakukan Pasien</div>
        <div class="tbl-scroll mb-2" style="max-height:130px;">
          <table class="table table-bordered table-sm mb-0">
            <thead><tr><th>No. Kwitansi</th><th style="width:140px;">Tgl Bayar</th><th class="num" style="width:160px;">Jumlah Bayar Rp</th></tr></thead>
            <tbody id="bc-bayar"><tr><td colspan="3" class="text-center text-muted">-</td></tr></tbody>
          </table>
        </div>

        <div class="row">
          <div class="col-md-5 mb-2">
            <div class="custom-control custom-checkbox mb-1"><input type="checkbox" class="custom-control-input" id="bc-auto" checked><label class="custom-control-label" for="bc-auto">Proses auto billing system</label></div>
            <div class="custom-control custom-checkbox mb-2"><input type="checkbox" class="custom-control-input" id="bc-page"><label class="custom-control-label" for="bc-page">Page option</label></div>
            <div class="form-row mb-1">
              <div class="col-4"><label>Kondisi Pulang</label></div>
              <div class="col-8"><input type="text" class="form-control form-control-sm" id="bc-kondisi"></div>
            </div>
            <div class="mb-2">
              <button type="button" class="btn btn-xs btn-default" disabled>Resume Medis</button>
              <button type="button" class="btn btn-xs btn-default" disabled>Klaim Sementara</button>
            </div>
            <div class="form-row">
              <div class="col-4"><label>Output To</label></div>
              <div class="col-8">
                <select class="form-control form-control-sm" id="bc-output">
                  <option value="layar">Layar</option><option value="printer">Printer</option>
                </select>
              </div>
            </div>
          </div>

          <div class="col-md-7 mb-2">
            <div class="form-row align-items-center mb-1">
              <div class="col-6 text-right"><label>Total Uang Muka Rp</label></div>
              <div class="col-6"><input type="text" id="bc-dp" class="form-control form-control-sm num" readonly value="0"></div>
            </div>
            <div class="form-row align-items-center mb-2">
              <div class="col-6 text-right"><label>Total Tagihan - Uang Muka Rp</label></div>
              <div class="col-6"><input type="text" id="bc-sisa" class="form-control form-control-sm num font-weight-bold" readonly value="0"></div>
            </div>
            <div class="small mb-1"><b>Admin Selisih Jaminan %</b>
              <label class="ml-2 mb-0"><input type="radio" name="bc-aj" checked> Total</label>
              <label class="ml-2 mb-0"><input type="radio" name="bc-aj"> Selisih</label>
              <label class="ml-2 mb-0"><input type="radio" name="bc-aj"> Dijamin</label>
              <label class="ml-2 mb-0"><input type="radio" name="bc-aj"> Noadm</label>
            </div>
            <div class="small"><b>Admin Selisih Tunai %</b>
              <label class="ml-2 mb-0"><input type="radio" name="bc-at" checked> Total</label>
              <label class="ml-2 mb-0"><input type="radio" name="bc-at"> Selisih</label>
              <label class="ml-2 mb-0"><input type="radio" name="bc-at"> Dijamin</label>
              <label class="ml-2 mb-0"><input type="radio" name="bc-at"> Noadm</label>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer py-2 justify-content-between">
        <button type="button" class="btn btn-warning btn-sm" disabled><i class="fa fa-list"></i> Cek Golongan</button>
        <div>
          <button type="button" class="btn btn-primary btn-sm" id="bc-cetak"><i class="fa fa-print"></i> Cetak</button>
          <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-sign-out-alt"></i> Keluar</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
(function () {
    var URL_BIAYA = <?= json_encode($urlBiaya) ?>;
    var URL_REG   = <?= json_encode($urlReg) ?>;
    var st = {kamar: [], tindakan: [], bayar: []};
    var $ = function (id) { return document.getElementById(id); };
    var nf = new Intl.NumberFormat('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    var fmt = function (n) { return nf.format(+n || 0); };
    var esc = function (s) { var d = document.createElement('div'); d.textContent = (s == null ? '' : s); return d.innerHTML; };
    var dmy = function (s) { if (!s) return ''; var p = String(s).substr(0, 10).split('-'); return p.length === 3 ? p[2] + '-' + p[1] + '-' + p[0] : s; };
    var catLabel = {ri: 'Rawat Inap', bedah: 'Bedah', ro: 'Radiologi', lab: 'Laboratorium', resep: 'Resep'};

    function getJson(url) { return fetch(url, {headers: {'Accept': 'application/json'}, credentials: 'same-origin'}).then(function (r) { return r.json(); }); }

    // ---------- cari registrasi ----------
    function searchReg() {
        var q = $('bc-reg').value.trim();
        var box = $('bc-results');
        if (q.length < 2) { box.style.display = 'none'; return; }
        getJson(URL_REG + '?q=' + encodeURIComponent(q)).then(function (rows) {
            if (!rows.length) { box.innerHTML = '<div class="p-2 text-muted">Tidak ditemukan</div>'; box.style.display = 'block'; return; }
            box.innerHTML = rows.map(function (r, i) {
                return '<a data-i="' + i + '"><b>' + esc(r.registration_no) + '</b> &middot; ' + esc(r.name) + '<br><small class="text-muted">' + esc(r.rm) + ' &middot; ' + esc(r.service_label) + '</small></a>';
            }).join('');
            box.style.display = 'block';
            box.querySelectorAll('a').forEach(function (a) {
                a.addEventListener('click', function () {
                    var r = rows[+a.dataset.i];
                    $('bc-reg').value = r.registration_no;
                    $('bc-rm').value = r.rm;
                    $('bc-nama').value = r.name;
                    box.style.display = 'none';
                    proses();
                });
            });
        });
    }
    $('bc-search').addEventListener('click', searchReg);
    $('bc-reg').addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); searchReg(); } });

    // ---------- proses / ambil data ----------
    function proses() {
        var reg = $('bc-reg').value.trim();
        if (!reg) { alert('Isi No Registrasi dulu.'); return; }
        var url = URL_BIAYA + '?registration_no=' + encodeURIComponent(reg);
        if ($('bc-sampai').value) url += '&sampai=' + $('bc-sampai').value;
        getJson(url).then(function (d) {
            if (d.error) { alert(d.error); return; }
            $('bc-reg').value = d.patient.registration_no || reg;
            $('bc-rm').value = d.patient.rm || '';
            $('bc-nama').value = d.patient.name || '';
            $('bc-tglmasuk').value = dmy(d.patient.tgl_masuk);
            $('bc-jammasuk').value = d.patient.jam_masuk || '';
            st.kamar = d.kamar || [];
            st.tindakan = d.tindakan || [];
            st.bayar = d.bayar || [];
            render();
        }).catch(function () { alert('Gagal mengambil data biaya.'); });
    }
    $('bc-proses').addEventListener('click', proses);
    $('bc-cek').addEventListener('click', proses);

    // ---------- render ----------
    function onlyChecked(cat) { var c = document.querySelector('.bc-only[data-cat="' + cat + '"]'); return c && c.checked; }

    function render() {
        var totKamar = 0, totTind = 0, totBayar = 0, html;

        html = st.kamar.map(function (k) {
            totKamar += +k.total || 0;
            return '<tr><td>' + esc(k.ruang) + '</td><td>' + esc(k.kelas) + '</td><td>' + dmy(k.tgl_masuk) + '</td><td>' + dmy(k.tgl_keluar) +
                '</td><td class="num">' + fmt(k.tarif) + '</td><td class="num">' + (k.hari || 0) + '</td><td class="num">' + fmt(k.total) + '</td></tr>';
        }).join('');
        $('bc-kamar').innerHTML = html || '<tr><td colspan="7" class="text-center text-muted">Tidak ada pemakaian kamar</td></tr>';

        var groups = {}, rows = [];
        st.tindakan.forEach(function (t) {
            totTind += +t.total || 0;
            if (onlyChecked(t.cat)) { groups[t.cat] = (groups[t.cat] || 0) + (+t.total || 0); }
            else rows.push(t);
        });
        html = rows.map(function (t) {
            return '<tr><td><code>' + esc(t.no) + '</code></td><td>' + esc(t.nama) + '</td><td>' + dmy(t.tgl) + '</td><td class="num">' + fmt(t.total) + '</td></tr>';
        }).join('');
        Object.keys(groups).forEach(function (c) {
            html += '<tr class="table-secondary"><td></td><td><b>Total ' + esc(catLabel[c] || c) + '</b></td><td></td><td class="num"><b>' + fmt(groups[c]) + '</b></td></tr>';
        });
        $('bc-tindakan').innerHTML = html || '<tr><td colspan="4" class="text-center text-muted">Tidak ada tindakan / obat</td></tr>';

        html = st.bayar.map(function (b) {
            totBayar += +b.jumlah || 0;
            return '<tr><td>' + esc(b.no) + '</td><td>' + dmy(b.tgl) + '</td><td class="num">' + fmt(b.jumlah) + '</td></tr>';
        }).join('');
        $('bc-bayar').innerHTML = html || '<tr><td colspan="3" class="text-center text-muted">Belum ada pembayaran</td></tr>';

        st.total = totKamar + totTind;
        st.dp = totBayar;
        recalcPct();
        calc();
    }

    // ---------- hitung ----------
    function num(id) { return parseFloat($(id).value) || 0; }
    function recalcPct() {
        $('bc-adm-rp').value = Math.round((st.total || 0) * num('bc-adm-pct')) / 100;
        $('bc-sub-rp').value = Math.round((st.total || 0) * num('bc-sub-pct')) / 100;
    }
    function calc() {
        var before = (st.total || 0) + num('bc-adm-rp') + num('bc-materai') - num('bc-sub-rp');
        $('bc-total').value = fmt(st.total);
        $('bc-before').value = fmt(before);
        $('bc-dp').value = fmt(st.dp);
        $('bc-sisa').value = fmt(before - (st.dp || 0));
    }
    $('bc-adm-pct').addEventListener('input', function () { recalcPct(); calc(); });
    $('bc-sub-pct').addEventListener('input', function () { recalcPct(); calc(); });
    $('bc-adm-rp').addEventListener('input', function () { $('bc-adm-pct').value = st.total ? (num('bc-adm-rp') / st.total * 100).toFixed(2) : 0; calc(); });
    $('bc-sub-rp').addEventListener('input', function () { $('bc-sub-pct').value = st.total ? (num('bc-sub-rp') / st.total * 100).toFixed(2) : 0; calc(); });
    $('bc-materai').addEventListener('input', calc);
    document.querySelectorAll('.bc-only').forEach(function (c) { c.addEventListener('change', render); });

    // ---------- cetak ----------
    $('bc-cetak').addEventListener('click', function () {
        var w = window.open('', '_blank', 'width=900,height=700');
        w.document.write('<html><head><title>Informasi Biaya Pasien</title><style>body{font-family:Arial;font-size:12px}table{width:100%;border-collapse:collapse;margin-bottom:8px}th,td{border:1px solid #999;padding:3px 5px}.num{text-align:right}.sec-title{background:#ddd;font-weight:bold;padding:3px}input{border:0;font-weight:bold}</style></head><body>' +
            '<h3>Informasi Biaya Pasien</h3>' +
            '<p>No Reg: <b>' + esc($('bc-reg').value) + '</b> &nbsp; RM: <b>' + esc($('bc-rm').value) + '</b> &nbsp; Nama: <b>' + esc($('bc-nama').value) + '</b></p>' +
            '<div class="sec-title">Pemakaian Kamar</div><table>' + $('bc-kamar').closest('table').innerHTML + '</table>' +
            '<div class="sec-title">Tindakan dan Pengobatan</div><table>' + $('bc-tindakan').closest('table').innerHTML + '</table>' +
            '<div class="sec-title">Uang Muka / Pembayaran</div><table>' + $('bc-bayar').closest('table').innerHTML + '</table>' +
            '<p>Total Keseluruhan: <b>' + $('bc-total').value + '</b><br>Administrasi: <b>' + fmt(num('bc-adm-rp')) + '</b> &nbsp; Materai: <b>' + fmt(num('bc-materai')) + '</b> &nbsp; Subsidi: <b>' + fmt(num('bc-sub-rp')) + '</b><br>' +
            'Total Tagihan: <b>' + $('bc-before').value + '</b><br>Total Uang Muka: <b>' + $('bc-dp').value + '</b><br>Sisa Tagihan: <b>' + $('bc-sisa').value + '</b></p>' +
            '</body></html>');
        w.document.close(); w.focus(); w.print();
    });

    // ---------- buka dari tombol "Biaya" di baris pasien ----------
    document.addEventListener('click', function (e) {
        var b = e.target.closest('.btn-biaya');
        if (!b) return;
        $('bc-reg').value = b.dataset.reg || '';
        $('bc-nama').value = b.dataset.nama || '';
        $('bc-rm').value = b.dataset.rm || '';
        if (window.jQuery) { jQuery('#modalBiaya').one('shown.bs.modal', proses).modal('show'); }
    });
    document.addEventListener('click', function (e) { if (!e.target.closest('#bc-results') && e.target.id !== 'bc-reg') $('bc-results').style.display = 'none'; });
})();
</script>
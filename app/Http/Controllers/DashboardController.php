<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $hariini = date("Y-m-d");
        $bulanini = date("m") * 1; //1 atau Januari
        $tahunini = date("Y"); // 2023
        $nik = Auth::guard('karyawan')->user()->nik;
        $presensihariini = DB::table('presensi')->where('nik', $nik)->where('tgl_presensi', $hariini)->first();
        $historibulanini = DB::table('presensi')
            ->select('presensi.*', 'keterangan', 'jam_kerja.*', 'doc_sid', 'nama_cuti')
            ->leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->leftJoin('pengajuan_izin', 'presensi.kode_izin', '=', 'pengajuan_izin.kode_izin')
            ->leftJoin('master_cuti', 'pengajuan_izin.kode_cuti', '=', 'master_cuti.kode_cuti')
            ->where('presensi.nik', $nik)
            ->whereRaw('MONTH(tgl_presensi)="' . $bulanini . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahunini . '"')
            ->orderBy('tgl_presensi')
            ->get();

        $rekappresensi = DB::table('presensi')
            ->selectRaw('COUNT(nik) as jmlhadir, SUM(IF(jam_in > jam_masuk ,1,0)) as jmlterlambat')
            ->leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('nik', $nik)
            ->whereRaw('MONTH(tgl_presensi)="' . $bulanini . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahunini . '"')
            ->first();


        $leaderboard = DB::table('presensi')
            ->join('karyawan', 'presensi.nik', '=', 'karyawan.nik')
            ->where('tgl_presensi', $hariini)
            ->orderBy('jam_in')
            ->get();
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        $rekapizin = DB::table('pengajuan_izin')
            ->selectRaw('SUM(IF(status="i",1,0)) as jmlizin,SUM(IF(status="s",1,0)) as jmlsakit')
            ->where('nik', $nik)
            ->whereRaw('MONTH(tgl_izin_dari)="' . $bulanini . '"')
            ->whereRaw('YEAR(tgl_izin_dari)="' . $tahunini . '"')
            ->where('status_approved', 1)
            ->first();


        return view('dashboard.dashboard', compact('presensihariini', 'historibulanini', 'namabulan', 'bulanini', 'tahunini', 'rekappresensi', 'leaderboard', 'rekapizin'));
    }

    public function dashboardadmin()
    {

        $bulanini = date("m");
        $tahunini = date("Y");
        $hariini = date("Y-m-d");
        $rekappresensi = DB::table('presensi')
            ->selectRaw('COUNT(nik) as jmlhadir, SUM(IF(jam_in > "07:00",1,0)) as jmlterlambat')
            ->where('tgl_presensi', $hariini)
            ->first();

        $rekapizin = DB::table('pengajuan_izin')
            ->selectRaw('SUM(IF(status="i",1,0)) as jmlizin,SUM(IF(status="s",1,0)) as jmlsakit')
            ->where('tgl_izin_dari', $hariini)
            ->where('status_approved', 1)
            ->first();

        $datagrafik = DB::table('presensi')
            ->selectRaw('
            SUM(IF(DAY(tgl_presensi)=1 AND jam_in IS NOT NULL,1,0)) as tgl_1,
            SUM(IF(DAY(tgl_presensi)=2 AND jam_in IS NOT NULL,1,0)) as tgl_2,
            SUM(IF(DAY(tgl_presensi)=3 AND jam_in IS NOT NULL,1,0)) as tgl_3,
            SUM(IF(DAY(tgl_presensi)=4 AND jam_in IS NOT NULL,1,0)) as tgl_4,
            SUM(IF(DAY(tgl_presensi)=5 AND jam_in IS NOT NULL,1,0)) as tgl_5,
            SUM(IF(DAY(tgl_presensi)=6 AND jam_in IS NOT NULL,1,0)) as tgl_6,
            SUM(IF(DAY(tgl_presensi)=7 AND jam_in IS NOT NULL,1,0)) as tgl_7,
            SUM(IF(DAY(tgl_presensi)=8 AND jam_in IS NOT NULL,1,0)) as tgl_8,
            SUM(IF(DAY(tgl_presensi)=9 AND jam_in IS NOT NULL,1,0)) as tgl_9,
            SUM(IF(DAY(tgl_presensi)=10 AND jam_in IS NOT NULL,1,0)) as tgl_10,
            SUM(IF(DAY(tgl_presensi)=11 AND jam_in IS NOT NULL,1,0)) as tgl_11,
            SUM(IF(DAY(tgl_presensi)=12 AND jam_in IS NOT NULL,1,0)) as tgl_12,
            SUM(IF(DAY(tgl_presensi)=13 AND jam_in IS NOT NULL,1,0)) as tgl_13,
            SUM(IF(DAY(tgl_presensi)=14 AND jam_in IS NOT NULL,1,0)) as tgl_14,
            SUM(IF(DAY(tgl_presensi)=15 AND jam_in IS NOT NULL,1,0)) as tgl_15,
            SUM(IF(DAY(tgl_presensi)=16 AND jam_in IS NOT NULL,1,0)) as tgl_16,
            SUM(IF(DAY(tgl_presensi)=17 AND jam_in IS NOT NULL,1,0)) as tgl_17,
            SUM(IF(DAY(tgl_presensi)=18 AND jam_in IS NOT NULL,1,0)) as tgl_18,
            SUM(IF(DAY(tgl_presensi)=19 AND jam_in IS NOT NULL,1,0)) as tgl_19,
            SUM(IF(DAY(tgl_presensi)=20 AND jam_in IS NOT NULL,1,0)) as tgl_20,
            SUM(IF(DAY(tgl_presensi)=21 AND jam_in IS NOT NULL,1,0)) as tgl_21,
            SUM(IF(DAY(tgl_presensi)=22 AND jam_in IS NOT NULL,1,0)) as tgl_22,
            SUM(IF(DAY(tgl_presensi)=23 AND jam_in IS NOT NULL,1,0)) as tgl_23,
            SUM(IF(DAY(tgl_presensi)=24 AND jam_in IS NOT NULL,1,0)) as tgl_24,
            SUM(IF(DAY(tgl_presensi)=25 AND jam_in IS NOT NULL,1,0)) as tgl_25,
            SUM(IF(DAY(tgl_presensi)=26 AND jam_in IS NOT NULL,1,0)) as tgl_26,
            SUM(IF(DAY(tgl_presensi)=27 AND jam_in IS NOT NULL,1,0)) as tgl_27,
            SUM(IF(DAY(tgl_presensi)=28 AND jam_in IS NOT NULL,1,0)) as tgl_28,
            SUM(IF(DAY(tgl_presensi)=29 AND jam_in IS NOT NULL,1,0)) as tgl_29,
            SUM(IF(DAY(tgl_presensi)=30 AND jam_in IS NOT NULL,1,0)) as tgl_30
        ')
            ->whereRaw('MONTH(tgl_presensi)="' . $bulanini . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahunini . '"')
            ->first();


        return view('dashboard.dashboardadmin', compact('rekappresensi', 'rekapizin', 'datagrafik'));
    }
}

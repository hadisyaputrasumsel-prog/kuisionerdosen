import csv
import re

def parse_jadwal_ruangan():
    with open('jadwal_ruangan.html', 'r', encoding='utf-8') as f:
        html = f.read()
        
    # Ambil Periode
    periode_match = re.search(r'Pada Semua Program Studi di Tahun Akademik\s*<span class="font-weight-semibold">([^<]+)</span>', html)
    periode = periode_match.group(1).strip() if periode_match else "2025/2026 GENAP REGULER"
            
    all_data = []
    
    # Ambil semua data popover. Formatnya:
    # <a href="#" class="text-white" data-popup="popover" title="JUMAT, 13:30 - 14:40" data-trigger="hover"  data-html="true" data-content="<div class='font-size-sm'>PRODI : <span class='font-weight-semibold'>ILMU KOMUNIKASI</span></div><div class='font-size-sm'>MATAKULIAH : <span class='font-weight-semibold'>KOMUNIKASI PEMASARAN TERPADU - 3 SKS</span></div><div class='font-size-sm'>RUANG : <span class='font-weight-semibold'>KAMPUS A ONLINE 1</span></div><div class='font-size-sm'>DOSEN : <span class='font-weight-semibold'>Feiza Salsabila Deka, S.I.Kom., M.I.Kom., </span></div>" data-placement="top">
    # 13:30 - 14:40
    # </a>
    
    matches = re.finditer(r'title="((?:SENIN|SELASA|RABU|KAMIS|JUMAT|SABTU|MINGGU)),\s*([^"]+)"[^>]+data-content="([^"]+)"', html)
    
    for match in matches:
        hari = match.group(1).strip()
        waktu = match.group(2).strip()
        content = match.group(3)
        
        # Ekstrak elemen dari content
        prodi_match = re.search(r'PRODI : <span class=\'font-weight-semibold\'>([^<]+)</span>', content)
        mk_match = re.search(r'MATAKULIAH : <span class=\'font-weight-semibold\'>([^<]+)</span>', content)
        ruang_match = re.search(r'RUANG : <span class=\'font-weight-semibold\'>([^<]+)</span>', content)
        dosen_match = re.search(r'DOSEN : <span class=\'font-weight-semibold\'>([^<]+)</span>', content)
        
        prodi = prodi_match.group(1).strip() if prodi_match else ""
        matakuliah = mk_match.group(1).strip() if mk_match else ""
        ruang = ruang_match.group(1).strip() if ruang_match else ""
        dosen = dosen_match.group(1).strip() if dosen_match else ""
        
        # Bersihkan koma di akhir nama dosen
        if dosen.endswith(','):
            dosen = dosen[:-1].strip()
            
        all_data.append({
            "Dosen": dosen,
            "Matakuliah": matakuliah,
            "Program Studi": prodi,
            "Kelas": "N/A",  # Kelas tidak tersedia di halaman jadwal ruangan
            "Periode": periode,
            "Hari": hari,
            "Waktu": waktu,
            "Ruang": ruang
        })
                
    # Tulis ke CSV
    with open('semua_jadwal_ruangan.csv', 'w', newline='', encoding='utf-8') as csvfile:
        fieldnames = ["Dosen", "Matakuliah", "Program Studi", "Kelas", "Periode", "Hari", "Waktu", "Ruang"]
        writer = csv.DictWriter(csvfile, fieldnames=fieldnames)
        
        writer.writeheader()
        for data in all_data:
            writer.writerow(data)
            
    print(f"Berhasil mengekstrak {len(all_data)} jadwal ke 'semua_jadwal_ruangan.csv'")

if __name__ == "__main__":
    parse_jadwal_ruangan()

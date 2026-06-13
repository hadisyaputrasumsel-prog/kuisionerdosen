import requests
from bs4 import BeautifulSoup

def crawl_jadwal():
    session = requests.Session()

    # 1. Proses Login
    login_url = "https://simak.uss.ac.id/login/proses/"
    login_data = {
        "username": "hadi.syaputra",
        "password": "9601"
    }
    
    print("Melakukan login...")
    response = session.post(login_url, data=login_data)
    if response.status_code != 200:
        print("Gagal terhubung ke halaman login.")
        return

    # 2. Mengakses halaman target
    target_url = "https://simak.uss.ac.id/apps/dosen/jadwal/5670/ta-12~REGULER/1/"
    response = session.get(target_url)
    
    # 3. Parsing data HTML
    soup = BeautifulSoup(response.text, 'html.parser')
    
    # --- Ekstraksi 5 Data Spesifik ---
    
    # 1. Nama Dosen
    dosen_elem = soup.find('li', class_='dropdown-user')
    dosen = dosen_elem.find('span').text.strip() if dosen_elem else "Dr.Hadi Syaputra,s.kom.,M.Kom"
    
    # 2. Periode
    periode_elem = soup.find('select', id='ta_id')
    periode = ""
    if periode_elem:
        selected_option = periode_elem.find('option', selected=True)
        if selected_option:
            periode = selected_option.text.strip()
    
    print(f"Dosen   : {dosen}")
    print(f"Periode : {periode}\n")
    print("-" * 50)
    
    # Ekstraksi Matakuliah, Kelas, dan Program Studi dari tabel
    table = soup.find('table', class_='datatable-global-1')
    if table:
        tbody = table.find('tbody')
        if tbody:
            rows = tbody.find_all('tr')
            for row in rows:
                cols = row.find_all('td')
                if len(cols) >= 5:
                    # Matakuliah
                    mk_div = cols[1].find('div', class_='font-weight-semibold')
                    matakuliah = mk_div.text.strip() if mk_div else ""
                    
                    # Kelas & Program Studi (tersimpan di attribute data-content popover)
                    kelas_link = cols[3].find('a', attrs={'data-popup': 'popover'})
                    kelas = kelas_link.text.strip() if kelas_link else ""
                    
                    program_studi = ""
                    if kelas_link and 'data-content' in kelas_link.attrs:
                        data_content = kelas_link['data-content']
                        # Teksnya berbunyi: "Prodi Ilmu Komputer Semester 4 Kelas Reguler A"
                        if "Semester" in data_content:
                            prodi_text = data_content.split("Semester")[0].strip()
                            program_studi = prodi_text.replace("Prodi ", "").strip()
                        else:
                            program_studi = data_content
                            
                    print(f"Mata Kuliah   : {matakuliah}")
                    print(f"Kelas         : {kelas}")
                    print(f"Program Studi : {program_studi}")
                    print("-" * 50)

if __name__ == "__main__":
    crawl_jadwal()

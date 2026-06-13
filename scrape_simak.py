import requests
from bs4 import BeautifulSoup
import json
import sys
from urllib3.util import connection

_orig_create_connection = connection.create_connection
def patched_create_connection(address, *args, **kwargs):
    host, port = address
    if host == 'simak.uss.ac.id':
        address = ('202.146.181.102', port)
    return _orig_create_connection(address, *args, **kwargs)
connection.create_connection = patched_create_connection

def crawl_jadwal():
    if len(sys.argv) < 3:
        print(json.dumps({"error": "Username dan Password SIMAK tidak diberikan."}))
        sys.exit(1)
        
    session = requests.Session()

    # 1. Proses Login
    login_url = "https://simak.uss.ac.id/login/proses/"
    login_data = {
        "username": sys.argv[1],
        "password": sys.argv[2]
    }
    
    try:
        response = session.post(login_url, data=login_data, timeout=10)
        if response.status_code != 200:
            print(json.dumps({"error": "Gagal terhubung ke halaman login (Status Code: " + str(response.status_code) + ")."}))
            sys.exit(1)

        # 2. Mengakses halaman Dashboard untuk mencari link Jadwal
        dashboard_url = "https://simak.uss.ac.id/apps/dashboard/dashboard/"
        response = session.get(dashboard_url, timeout=10)
        soup_dash = BeautifulSoup(response.text, 'html.parser')
        
        # Cari link yang mengarah ke /dosen/jadwal/
        jadwal_link = soup_dash.find('a', href=lambda href: href and '/apps/dosen/jadwal/' in href)
        
        if not jadwal_link:
            print(json.dumps({"error": "Tidak dapat menemukan menu Jadwal Mengajar di dashboard SIMAK. Pastikan akun ini adalah akun dosen."}))
            sys.exit(1)
            
        from urllib.parse import urljoin
        target_url = urljoin(dashboard_url, jadwal_link['href'])
        
        # 3. Mengakses halaman target (Jadwal)
        response = session.get(target_url, timeout=10)
        
        target_periode_text = sys.argv[3] if len(sys.argv) > 3 else None
        if target_periode_text:
            soup_jadwal = BeautifulSoup(response.text, 'html.parser')
            periode_elem = soup_jadwal.find('select', id='ta_id')
            if periode_elem:
                target_value = None
                normalized_target = target_periode_text.replace(" ", "").lower()
                for option in periode_elem.find_all('option'):
                    if option.text.replace(" ", "").lower() == normalized_target:
                        target_value = option.get('value')
                        break
                
                if target_value:
                    # Switch period by submitting the form.
                    form = periode_elem.find_parent('form')
                    if form:
                        form_action = form.get('action') or target_url
                        if not form_action.startswith('http'):
                            from urllib.parse import urljoin
                            form_action = urljoin(target_url, form_action)
                            
                        form_method = form.get('method', 'GET').upper()
                        
                        # collect all inputs
                        form_data = {}
                        for input_tag in form.find_all(['input', 'select']):
                            name = input_tag.get('name')
                            if not name:
                                continue
                            if name == 'ta_id':
                                form_data[name] = target_value
                            elif input_tag.name == 'select':
                                selected = input_tag.find('option', selected=True)
                                if selected:
                                    form_data[name] = selected.get('value', '')
                                else:
                                    first_opt = input_tag.find('option')
                                    form_data[name] = first_opt.get('value', '') if first_opt else ''
                            else:
                                value = input_tag.get('value', '')
                                form_data[name] = value
                                
                        with open("debug_form.json", "w") as f:
                            json.dump({
                                "form_action": form_action,
                                "form_method": form_method,
                                "form_data": form_data
                            }, f)

                        if form_method == 'POST':
                            response = session.post(form_action, data=form_data, timeout=10)
                        else:
                            response = session.get(form_action, params=form_data, timeout=10)
                        
    except requests.exceptions.RequestException as e:
        print(json.dumps({"error": "Gagal terhubung ke server SIMAK. Pastikan komputer terhubung ke internet atau jaringan kampus. Error: " + str(e)}))
        sys.exit(1)
        
    # 3. Parsing data HTML
    soup = BeautifulSoup(response.text, 'html.parser')
    
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
    
    data = []
    
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
                            
                    data.append({
                        "dosen": dosen,
                        "periode": periode,
                        "mata_kuliah": matakuliah,
                        "kelas": kelas,
                        "program_studi": program_studi
                    })
                    
    print(json.dumps({"status": "success", "data": data}))

if __name__ == "__main__":
    crawl_jadwal()

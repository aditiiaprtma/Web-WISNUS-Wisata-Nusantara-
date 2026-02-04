/**
 * Fungsi Filter/Pencarian Wisata
 * Dijalankan setiap kali user mengetik atau mengubah kategori
 */
function searchMovie() {
    // 1. Ambil input dari user
    const keyword = document.getElementById("title").value.toLowerCase();
    const kategoriTerpilih = document.getElementById("kategori").value;
    
    // 2. Ambil semua elemen kartu wisata
    const items = document.querySelectorAll(".wisata-item");
    let ditemukan = 0;

    items.forEach(item => {
        // Ambil data dari atribut HTML yang dibuat PHP
        const namaDanDaerah = item.getAttribute("data-nama") || "";
        const kategori = item.getAttribute("data-kategori") || "";
        
        // Logika Filter
        const cocokKeyword = namaDanDaerah.includes(keyword);
        const cocokKategori = kategoriTerpilih === "" || kategori === kategoriTerpilih;

        // 3. Tampilkan atau Sembunyikan
        if (cocokKeyword && cocokKategori) {
            item.style.display = "block";
            ditemukan++;
        } else {
            item.style.display = "none";
        }
    });

    // 4. Kelola pesan "Tidak Ditemukan"
    toggleEmptyMessage(ditemukan);
}

function toggleEmptyMessage(jumlahData) {
    const resultDiv = document.getElementById("result");
    let msgElement = document.getElementById("no-results-msg");

    if (jumlahData === 0) {
        if (!msgElement) {
            msgElement = document.createElement("p");
            msgElement.id = "no-results-msg";
            msgElement.style.textAlign = "center";
            msgElement.style.gridColumn = "1/-1";
            msgElement.innerHTML = "🔍 Destinasi tidak ditemukan.";
            resultDiv.appendChild(msgElement);
        }
    } else {
        if (msgElement) msgElement.remove();
    }
}
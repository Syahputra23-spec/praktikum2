        </section>
    </main>
</div>

<script>
// Konfirmasi Aksi (misal Hapus)
document.querySelectorAll('[data-confirm]').forEach(function (el) {
    el.addEventListener('click', function (event) {
        if (!confirm(el.getAttribute('data-confirm'))) {
            event.preventDefault();
        }
    });
});

// Pencarian Realtime Tabel
const searchInput = document.getElementById('tableSearch');
if (searchInput) {
    searchInput.addEventListener('input', function () {
        const keyword = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#alatTable tbody tr[data-row]');
        let visibleCount = 0;

        rows.forEach(function (row) {
            const match = row.innerText.toLowerCase().includes(keyword);
            row.style.display = match ? '' : 'none';
            if (match) visibleCount++;
        });

        // Update footer teks jika ada
        const panelFoot = document.querySelector('.panel-foot');
        if (panelFoot) {
            if (keyword !== '') {
                panelFoot.innerHTML = `Ditemukan <strong>${visibleCount}</strong> dari total data`;
            } else {
                panelFoot.innerHTML = `Menampilkan <strong>${rows.length}</strong> data alat elektromedis`;
            }
        }
    });
}

// Toggle Sidebar Mobile
const menuToggle = document.getElementById('menuToggle');
const sidebar = document.getElementById('sidebar');
const sidebarOverlay = document.getElementById('sidebarOverlay');

if (menuToggle && sidebar && sidebarOverlay) {
    menuToggle.addEventListener('click', function() {
        sidebar.classList.toggle('active');
        sidebarOverlay.classList.toggle('active');
    });

    sidebarOverlay.addEventListener('click', function() {
        sidebar.classList.remove('active');
        sidebarOverlay.classList.remove('active');
    });
}

// Auto dismiss notifikasi setelah 5 detik
const notices = document.querySelectorAll('.notice');
notices.forEach(function(notice) {
    setTimeout(function() {
        notice.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        notice.style.opacity = '0';
        notice.style.transform = 'translateY(-10px)';
        setTimeout(function() { notice.remove(); }, 500);
    }, 5000);
});
</script>
</body>
</html>

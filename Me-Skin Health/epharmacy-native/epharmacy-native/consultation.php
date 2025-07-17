<?php
// consultation.php

$page = $_GET['page'] ?? 'list';
$nama_dokter = isset($_GET['dokter']) ? htmlspecialchars($_GET['dokter']) : 'Dokter';

// Data Dokter (bisa dipindahkan ke database nantinya)
$dokter_list = [
  ['id' => 1, 'nama' => 'Tompi', 'spesialis' => 'Bedah Plastik', 'foto' => './image/tompi.jpg'],
  ['id' => 2, 'nama' => 'Boyke Dian Nugraha', 'spesialis' => 'Kandungan', 'foto' => './image/dokter-boyke-soal-kpai.jpg'],
  ['id' => 3, 'nama' => 'Reisa Broto Asmoro', 'spesialis' => 'Spesialis', 'foto' => './image/reisa-broto-asmoro-jelaskan-perbedaan-vaksinasi-dan-imunisasi-cfi.jpg'],
  ['id' => 4, 'nama' => 'Indah Kusuma', 'spesialis' => 'Dokter Umum', 'foto' => './image/indahh.jpg'],
  ['id' => 5, 'nama' => 'Mesty Ariotedjo', 'spesialis' => 'Spesialis Anak', 'foto' => './image/intip-sumber-kekayaan-mesty-ariotedjo-dokter-pendiri-2-startup-kesehatan-srpJPtgyZY.jpg'],
  ['id' => 6, 'nama' => 'Ivan', 'spesialis' => 'Dokter Obgyn', 'foto' => './image/dr-ivan.jpg'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Pharmacy | Konsultasi</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .chat-container { max-width: 600px; margin: auto; background: #fff; border: var(--border); border-radius: .8rem; box-shadow: var(--box-shadow); padding: 2rem; font-size: 1.4rem; }
        .chat-heading { text-align: center; font-size: 3rem; margin-bottom: 2rem; color: var(--black); text-transform: uppercase; text-shadow: var(--text-shadow); }
        .chat-heading span { color: var(--green); }
        #chatlog { max-height: 300px; overflow-y: auto; margin-bottom: 1.5rem; padding-right: 1rem; color: var(--black); font-size: 1.5rem; line-height: 1.6; }
        #userInput { width: 100%; padding: 1rem; border: var(--border); border-radius: .5rem; font-size: 1.4rem; color: var(--black); }
    </style>
</head>
<body>

<?php if ($page == 'list'): ?>
<section class="doctors" style="padding-top: 8rem;">
    <h1 class="heading">Pilih <span>Dokter</span></h1>
    <div class="box-container">
      <?php foreach ($dokter_list as $d): ?>
        <div class="box">
          <img src="<?= $d['foto'] ?>" alt="Foto <?= htmlspecialchars($d['nama']) ?>">
          <h3><?= htmlspecialchars($d['nama']) ?></h3>
          <span><?= htmlspecialchars($d['spesialis']) ?></span><br><br>
          <a href="consultation.php?page=chat&dokter=<?= urlencode($d['nama']) ?>" class="btn">Chat Sekarang</a>
        </div>
      <?php endforeach; ?>
    </div>
</section>

<?php elseif ($page == 'chat'): ?>
<section class="chat-section" style="padding-top: 8rem;">
    <h2 class="chat-heading">Chat dengan <span><?= $nama_dokter ?></span></h2>
    <div class="chat-container">
        <div id="chatlog">
            <div style="margin-bottom: 1rem;">
                <strong style="color: var(--black);"><?= $nama_dokter ?>:</strong> Halo! Ada yang bisa saya bantu?
            </div>
        </div>
        <input type="text" id="userInput" placeholder="Tulis pesan..." onkeypress="if(event.key === 'Enter') sendMessage()">
    </div>
</section>

<script>
    const namaDokter = "<?= $nama_dokter ?>";
    function sendMessage() {
        const input = document.getElementById('userInput');
        const chatlog = document.getElementById('chatlog');
        const message = input.value.trim();
        if (message === '') return;

        chatlog.innerHTML += `<div style="margin-bottom: .5rem;"><strong style="color: var(--green);">Anda:</strong> ${message}</div>`;
        const response = getBotResponse(message);
        chatlog.innerHTML += `<div style="margin-bottom: 1rem;"><strong style="color: var(--black);">${namaDokter}:</strong> ${response}</div>`;
        input.value = '';
        chatlog.scrollTop = chatlog.scrollHeight;
    }

    function getBotResponse(input) {
        input = input.toLowerCase();
        if (input.includes("halo") || input.includes("hai")) return "Halo! Ada yang bisa saya bantu terkait keluhan Anda?";
        else if (input.includes("obat") && input.includes("sakit kepala")) return "Untuk sakit kepala ringan, Anda bisa mencoba Paracetamol. Namun jika berkelanjutan, periksakan diri Anda.";
        else if (input.includes("resep")) return "Saya tidak bisa memberikan resep tanpa diagnosa lengkap. Silakan ceritakan keluhan Anda lebih detail.";
        else if (input.includes("terima kasih")) return "Sama-sama! Semoga lekas sembuh. 😊";
        else return "Maaf, saya belum mengerti. Mohon jelaskan keluhan medis Anda agar saya bisa bantu.";
    }
</script>
<?php endif; ?>

</body>
</html>
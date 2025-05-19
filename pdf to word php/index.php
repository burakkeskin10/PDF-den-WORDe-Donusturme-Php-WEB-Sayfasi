<?php include("header.php"); ?>

<div class="pdf-container">
    <h2>📄 PDF to Word</h2>

    <form action="upload.php" method="post" enctype="multipart/form-data" id="uploadForm">
        <input type="file" name="pdfFile" id="pdfFile" class="hidden-upload" accept=".pdf" required>
        <button type="button" class="custom-upload-label" onclick="document.getElementById('pdfFile').click()">📎 Dosya Seç</button>
        <p id="fileName" class="file-name"></p>
        <br>
        <button type="submit" id="convertBtn" class="pdf-btn" disabled>Dönüştür</button>
    </form>

    <div id="loadingSpinner" class="loading-spinner" style="display:none;">
        <div class="spinner"></div>
        <p style="margin-top:10px;">Yükleniyor, lütfen bekleyin...</p>
    </div>

    <?php if (isset($_GET['message'])): ?>
        <div class="pdf-message">
            <?php
                $success = isset($_GET['success']) && $_GET['success'] === 'true';
                echo $success ? $_GET['message'] : '<p style="color:red;">' . htmlspecialchars($_GET['message']) . '</p>';
            ?>
        </div>

        <?php if ($success): ?>
            <script>
                // Başarılı dönüşüm sonrası "Dönüştür" butonu devre dışı bırakılır
                document.addEventListener("DOMContentLoaded", function () {
                    document.getElementById("convertBtn").disabled = true;
                });
            </script>
        <?php endif; ?>
    <?php endif; ?>

    <p style="color: #888; font-size: 14px; margin-top: 10px;">
        En fazla 100 MB boyutunda PDF dosyası yükleyebilirsiniz.
    </p>
</div>

<script>
    const fileInput = document.getElementById("pdfFile");
    const fileNameLabel = document.getElementById("fileName");
    const convertBtn = document.getElementById("convertBtn");
    const uploadForm = document.getElementById("uploadForm");

    fileInput.addEventListener("change", function () {
        if (fileInput.files.length > 0) {
            fileNameLabel.textContent = "Seçilen dosya: " + fileInput.files[0].name;
            convertBtn.disabled = false;
        } else {
            fileNameLabel.textContent = "";
            convertBtn.disabled = true;
        }
    });

    uploadForm.addEventListener("submit", function () {
        document.getElementById("loadingSpinner").style.display = "block";
    });
</script>

<?php include("footer.php"); ?>

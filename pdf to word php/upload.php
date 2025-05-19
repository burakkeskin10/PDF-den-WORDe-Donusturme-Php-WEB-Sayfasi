<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_FILES["pdfFile"]) || $_FILES["pdfFile"]["error"] !== UPLOAD_ERR_OK) {
        header("Location: index.php?message=" . urlencode("Lütfen bir PDF dosyası seçin.") . "&success=false");
        exit;
    }

    $file = $_FILES["pdfFile"];
    $fileName = $file["name"];
    $fileTmpPath = $file["tmp_name"];
    $fileSize = $file["size"];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if ($fileExt !== "pdf") {
        header("Location: index.php?message=" . urlencode("Sadece PDF dosyası yükleyebilirsiniz.") . "&success=false");
        exit;
    }

    if ($fileSize > 100 * 1024 * 1024) { // 100 MB
        header("Location: index.php?message=" . urlencode("Dosya boyutu 100 MB'dan büyük olamaz.") . "&success=false");
        exit;
    }

    try {
        $secretKey = "secret_UdRcRD0FTyYa7MKW"; // ConvertAPI Key
        $fileData = file_get_contents($fileTmpPath);
        $base64File = base64_encode($fileData);

        $payload = [
            "Parameters" => [
                [
                    "Name" => "File",
                    "FileValue" => [
                        "Name" => $fileName,
                        "Data" => $base64File
                    ]
                ],
                [
                    "Name" => "StoreFile",
                    "Value" => true
                ]
            ]
        ];

        $jsonPayload = json_encode($payload);

        $ch = curl_init("https://v2.convertapi.com/convert/pdf/to/docx");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer $secretKey"
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            header("Location: index.php?message=" . urlencode("API hatası: $response") . "&success=false");
            exit;
        }

        $result = json_decode($response, true);
        $downloadUrl = $result["Files"][0]["Url"];

        header("Location: index.php?message=" . urlencode("<a href='$downloadUrl' target='_blank' class='download-btn'>İndir</a>") . "&success=true");
        exit;

    } catch (Exception $e) {
        header("Location: index.php?message=" . urlencode("Hata oluştu: " . $e->getMessage()) . "&success=false");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}

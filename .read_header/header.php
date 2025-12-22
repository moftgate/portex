<?php

class FileHeaderReader
{
    // Dosya tipini magic number'a göre belirle
    public function getFileType($filepath)
    {
        $handle = fopen($filepath, 'rb');
        if (!$handle) {
            return false;
        }

        $header = fread($handle, 8);
        fclose($handle);

        $signatures = [
            'PDF' => ['25504446', '%PDF'],
            'ZIP/DOCX' => ['504B0304', '504B0506'],
            'PNG' => ['89504E47'],
            'JPEG' => ['FFD8FF'],
            'GIF' => ['474946383761', '474946383961'],
            'DOC' => ['D0CF11E0A1B11AE1'],
        ];

        $hex = bin2hex($header);

        foreach ($signatures as $type => $sigs) {
            foreach ($sigs as $sig) {
                if (strpos($hex, strtolower($sig)) === 0) {
                    return $type;
                }
            }
        }

        return 'Unknown';
    }

    // PDF header bilgilerini oku
    public function readPdfHeader($filepath)
    {
        $handle = fopen($filepath, 'rb');
        if (!$handle) {
            return false;
        }

        $header = fread($handle, 1024);
        fclose($handle);

        $info = [];

        // PDF versiyonu
        if (preg_match('/%PDF-(\d\.\d)/', $header, $matches)) {
            $info['version'] = $matches[1];
        }

        // Title
        if (preg_match('/\/Title\s*\(([^\)]+)\)/', $header, $matches)) {
            $info['title'] = $matches[1];
        }

        // Author
        if (preg_match('/\/Author\s*\(([^\)]+)\)/', $header, $matches)) {
            $info['author'] = $matches[1];
        }

        // Creator
        if (preg_match('/\/Creator\s*\(([^\)]+)\)/', $header, $matches)) {
            $info['creator'] = $matches[1];
        }

        return $info;
    }

    // Word (DOCX) header bilgilerini oku
    public function readDocxMetadata($filepath)
    {
        if (!class_exists('ZipArchive')) {
            return ['error' => 'ZipArchive extension gerekli'];
        }

        $zip = new ZipArchive();
        if ($zip->open($filepath) !== true) {
            return false;
        }

        $metadata = [];

        // core.xml dosyasını oku
        $coreXml = $zip->getFromName('docProps/core.xml');
        if ($coreXml) {
            $xml = simplexml_load_string($coreXml);
            $namespaces = $xml->getNamespaces(true);

            if (isset($xml->children($namespaces['dc'])->title)) {
                $metadata['title'] = (string)$xml->children($namespaces['dc'])->title;
            }
            if (isset($xml->children($namespaces['dc'])->creator)) {
                $metadata['creator'] = (string)$xml->children($namespaces['dc'])->creator;
            }
            if (isset($xml->children($namespaces['cp'])->lastModifiedBy)) {
                $metadata['lastModifiedBy'] = (string)$xml->children($namespaces['cp'])->lastModifiedBy;
            }
        }

        $zip->close();
        return $metadata;
    }

    // Genel dosya bilgileri
    public function getFileInfo($filepath)
    {
        if (!file_exists($filepath)) {
            return ['error' => 'Dosya bulunamadı'];
        }

        return [
            'filename' => basename($filepath),
            'size' => filesize($filepath),
            'type' => $this->getFileType($filepath),
            'mime' => mime_content_type($filepath),
            'created' => filectime($filepath),
            'modified' => filemtime($filepath),
        ];
    }
}

// Kullanım örneği
$reader = new FileHeaderReader();

$file = 'example.pdf';

// Genel bilgiler
$info = $reader->getFileInfo($file);
echo "Dosya Bilgileri:\n";
print_r($info);

// PDF ise özel bilgiler
if ($info['type'] === 'PDF') {
    $pdfInfo = $reader->readPdfHeader($file);
    echo "\nPDF Header Bilgileri:\n";
    print_r($pdfInfo);
}

// DOCX ise özel bilgiler
if (str_contains($file, '.docx')) {
    $docxInfo = $reader->readDocxMetadata($file);
    echo "\nDOCX Metadata:\n";
    print_r($docxInfo);
}

?>

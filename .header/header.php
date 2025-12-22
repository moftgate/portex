<?php

class FileMetadataReader {

    /**
     * PDF dosyasındaki tüm metadata ve tagleri oku
     */
    public function readPdfMetadata($filepath) {
        $content = file_get_contents($filepath);

        $metadata = [
            'info' => [],
            'xmp' => [],
            'custom_properties' => []
        ];

        // Info Dictionary (Standart PDF metadata)
        $infoPattern = '/<\/Info\s+\d+\s+\d+\s+obj.*?>>|\/Info.*?>>|<<\/[^>]*>>/s';
        if (preg_match($infoPattern, $content, $matches)) {
            $infoBlock = $matches[0];

            // Tüm metadata alanlarını çek
            $fields = [
                'Title', 'Author', 'Subject', 'Keywords', 'Creator',
                'Producer', 'CreationDate', 'ModDate', 'Trapped'
            ];

            foreach ($fields as $field) {
                // Parantez içindeki değerler
                if (preg_match("/\/$field\s*\(([^\)]*)\)/", $infoBlock, $match)) {
                    $metadata['info'][$field] = $this->decodePdfString($match[1]);
                }
                // Hex string değerler
                elseif (preg_match("/\/$field\s*<([^>]*)>/", $infoBlock, $match)) {
                    $metadata['info'][$field] = hex2bin($match[1]);
                }
            }
        }

        // XMP Metadata (XML tabanlı gelişmiş metadata)
        if (preg_match('/<x:xmpmeta.*?<\/x:xmpmeta>/s', $content, $xmpMatch)) {
            $xmpContent = $xmpMatch[0];
            $metadata['xmp'] = $this->parseXmpMetadata($xmpContent);
        }

        // Custom Properties ve Keywords
        if (preg_match_all('/\/Keywords\s*\(([^\)]+)\)/', $content, $keywordMatches)) {
            $keywords = [];
            foreach ($keywordMatches[1] as $kw) {
                $keywords[] = $this->decodePdfString($kw);
            }
            $metadata['keywords'] = array_unique($keywords);
        }

        return $metadata;
    }

    /**
     * XMP metadata parse et
     */
    private function parseXmpMetadata($xmpContent) {
        $xmp = [];

        // Dublin Core metadata
        $dcFields = [
            'dc:title' => 'title',
            'dc:creator' => 'creator',
            'dc:subject' => 'subject',
            'dc:description' => 'description',
            'dc:publisher' => 'publisher',
            'dc:contributor' => 'contributor',
            'dc:date' => 'date',
            'dc:type' => 'type',
            'dc:format' => 'format',
            'dc:identifier' => 'identifier',
            'dc:source' => 'source',
            'dc:language' => 'language',
            'dc:relation' => 'relation',
            'dc:coverage' => 'coverage',
            'dc:rights' => 'rights'
        ];

        foreach ($dcFields as $tag => $key) {
            if (preg_match("/<$tag>(.*?)<\/$tag>/s", $xmpContent, $match)) {
                $xmp['dublin_core'][$key] = trim(strip_tags($match[1]));
            }
        }

        // PDF specific metadata
        $pdfFields = [
            'pdf:Keywords' => 'keywords',
            'pdf:Producer' => 'producer',
            'pdf:PDFVersion' => 'pdf_version'
        ];

        foreach ($pdfFields as $tag => $key) {
            if (preg_match("/<$tag>(.*?)<\/$tag>/s", $xmpContent, $match)) {
                $xmp['pdf'][$key] = trim($match[1]);
            }
        }

        // Adobe XMP metadata
        $xmpFields = [
            'xmp:CreateDate' => 'create_date',
            'xmp:ModifyDate' => 'modify_date',
            'xmp:MetadataDate' => 'metadata_date',
            'xmp:CreatorTool' => 'creator_tool'
        ];

        foreach ($xmpFields as $tag => $key) {
            if (preg_match("/<$tag>(.*?)<\/$tag>/s", $xmpContent, $match)) {
                $xmp['xmp'][$key] = trim($match[1]);
            }
        }

        return $xmp;
    }

    /**
     * DOCX dosyasındaki tüm metadata ve tagleri oku
     */
    public function readDocxMetadata($filepath) {
        if (!class_exists('ZipArchive')) {
            return ['error' => 'ZipArchive extension gerekli'];
        }

        $zip = new ZipArchive();
        if ($zip->open($filepath) !== true) {
            return false;
        }

        $metadata = [
            'core' => [],
            'app' => [],
            'custom' => []
        ];

        // Core Properties (docProps/core.xml)
        $coreXml = $zip->getFromName('docProps/core.xml');
        if ($coreXml) {
            $metadata['core'] = $this->parseDocxCore($coreXml);
        }

        // Extended Properties (docProps/app.xml)
        $appXml = $zip->getFromName('docProps/app.xml');
        if ($appXml) {
            $metadata['app'] = $this->parseDocxApp($appXml);
        }

        // Custom Properties (docProps/custom.xml)
        $customXml = $zip->getFromName('docProps/custom.xml');
        if ($customXml) {
            $metadata['custom'] = $this->parseDocxCustom($customXml);
        }

        $zip->close();
        return $metadata;
    }

    /**
     * DOCX Core properties parse et
     */
    private function parseDocxCore($xmlContent) {
        $core = [];

        // XML namespace'leri kaldır
        $xmlContent = preg_replace('/xmlns[^=]*="[^"]*"/i', '', $xmlContent);
        $xml = simplexml_load_string($xmlContent);

        if ($xml) {
            $fields = [
                'title', 'subject', 'creator', 'keywords',
                'description', 'lastModifiedBy', 'revision',
                'created', 'modified', 'category', 'contentStatus'
            ];

            foreach ($fields as $field) {
                if (isset($xml->$field)) {
                    $core[$field] = (string)$xml->$field;
                }
            }
        }

        return $core;
    }

    /**
     * DOCX App properties parse et
     */
    private function parseDocxApp($xmlContent) {
        $app = [];

        $xmlContent = preg_replace('/xmlns[^=]*="[^"]*"/i', '', $xmlContent);
        $xml = simplexml_load_string($xmlContent);

        if ($xml) {
            $fields = [
                'Application', 'AppVersion', 'Company', 'Manager',
                'Pages', 'Words', 'Characters', 'Lines', 'Paragraphs',
                'Template', 'TotalTime', 'DocSecurity'
            ];

            foreach ($fields as $field) {
                if (isset($xml->$field)) {
                    $app[$field] = (string)$xml->$field;
                }
            }
        }

        return $app;
    }

    /**
     * DOCX Custom properties parse et
     */
    private function parseDocxCustom($xmlContent) {
        $custom = [];

        $xmlContent = preg_replace('/xmlns[^=]*="[^"]*"/i', '', $xmlContent);
        $xml = simplexml_load_string($xmlContent);

        if ($xml) {
            foreach ($xml->property as $property) {
                $name = (string)$property['name'];

                // Değer tipine göre oku
                if (isset($property->lpwstr)) {
                    $custom[$name] = (string)$property->lpwstr;
                } elseif (isset($property->i4)) {
                    $custom[$name] = (int)$property->i4;
                } elseif (isset($property->bool)) {
                    $custom[$name] = (string)$property->bool === 'true';
                } elseif (isset($property->filetime)) {
                    $custom[$name] = (string)$property->filetime;
                }
            }
        }

        return $custom;
    }

    /**
     * PDF string decode et
     */
    private function decodePdfString($str) {
        // UTF-16BE BOM kontrolü
        if (substr($str, 0, 2) === "\xFE\xFF") {
            return mb_convert_encoding(substr($str, 2), 'UTF-8', 'UTF-16BE');
        }
        return $str;
    }

    /**
     * Metadata'yı güzel formatlı göster
     */
    public function displayMetadata($metadata, $indent = 0) {
        $spacing = str_repeat('  ', $indent);

        foreach ($metadata as $key => $value) {
            if (is_array($value)) {
                echo $spacing . ucfirst($key) . ":\n";
                $this->displayMetadata($value, $indent + 1);
            } else {
                echo $spacing . ucfirst($key) . ": " . $value . "\n";
            }
        }
    }
}

// Kullanım Örneği
echo "=== DOSYA METADATA VE TAG OKUYUCU ===\n\n";

$reader = new FileMetadataReader();

// PDF örneği
$pdfFile = 'example.pdf';
if (file_exists($pdfFile)) {
    echo "PDF METADATA VE TAGLER: $pdfFile\n";
    echo str_repeat('=', 50) . "\n";

    $pdfMetadata = $reader->readPdfMetadata($pdfFile);
    $reader->displayMetadata($pdfMetadata);

    echo "\n";
}

$file = '/Users/aras/Downloads/ÖzelNitelikliKişisel.pdf';


if (file_exists($file)) {
    echo "METADATA VE TAGLER: $file\n";
    echo str_repeat('=', 50) . "\n";

    $metadata = $reader->readPdfMetadata($file);
    $reader->displayMetadata($metadata);
}

// JSON formatında çıktı almak için:
echo json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

?>

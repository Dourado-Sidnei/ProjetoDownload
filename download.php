<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['video_url']) && filter_var($_POST['video_url'], FILTER_VALIDATE_URL)) {
        $videoUrl = escapeshellarg($_POST['video_url']);
        $format = $_POST['format'] ?? 'mp4'; // Padrão para MP4 se não for enviado

        // Caminho do FFmpeg no Windows
        $ffmpegPath = escapeshellarg('C:\\xampp\\htdocs\\ProjetoDownload\\ffmpeg\\bin\\ffmpeg.exe');

        // Define o comando baseado no formato
        $outputFile = ($format === 'mp3') ? 'audio.mp3' : 'video.mp4';
        $command = ($format === 'mp3') 
            ? "yt-dlp --ffmpeg-location $ffmpegPath -x --audio-format mp3 -o $outputFile $videoUrl"
            : "yt-dlp --ffmpeg-location $ffmpegPath -f best -o $outputFile $videoUrl";

        exec($command, $output, $status);

        if ($status === 0 && file_exists($outputFile)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($outputFile) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($outputFile));
            readfile($outputFile);
            unlink($outputFile);
            exit;
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Falha ao baixar o vídeo ou áudio.']);
            exit;
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'URL inválida fornecida.']);
        exit;
    }
}
?>

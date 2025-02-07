document.querySelector('form').addEventListener('submit', async function (e) {
    e.preventDefault();
    const url = document.querySelector('input[name="video_url"]').value;
    const format = e.submitter.value; // Pego o valor do botão clicado

    document.getElementById('loading').style.display = 'flex';

    if (!url || !url.startsWith('http')) {
        alert('Por favor, insira uma URL válida.');
        document.getElementById('loading').style.display = 'none';
        return;
    }

    const formData = new URLSearchParams();
    formData.append('video_url', url);
    formData.append('format', format);

    try {
        const response = await fetch('download.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData,
        });

        if (response.ok) {
            const blob = await response.blob();
            const downloadUrl = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = downloadUrl;
            a.download = format === 'mp3' ? 'audio.mp3' : 'video.mp4';
            document.body.appendChild(a);
            a.click();
            a.remove();
            URL.revokeObjectURL(downloadUrl);
        } else {
            const error = await response.json();
            alert(`Erro: ${error.error}`);
        }
    } catch (error) {
        console.error('Erro:', error);
        alert('Ocorreu um erro ao tentar baixar o arquivo.');
    } finally {
        document.getElementById('loading').style.display = 'none';
    }
});

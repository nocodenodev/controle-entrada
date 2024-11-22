<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Scanner de QR Code</title>
  <style>
    #scanner-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100vh;
      padding: 20px;
    }
    #message {
      margin-top: 15px;
      font-size: 18px;
      text-align: center;
    }
    #reader {
      width: 100%;
      max-width: 500px;
      border: 1px solid #000;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>

  <div id="scanner-container">
    <div id="reader"></div> <!-- Scanner vai ser renderizado aqui -->
    <div id="message">Posicione o QR Code à frente da câmera</div>
  </div>

  <!-- Inclusão da biblioteca -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.4/html5-qrcode.min.js" integrity="sha512-k/KAe4Yff9EUdYI5/IAHlwUswqeipP+Cp5qnrsUjTPCgl51La2/JhyyjNciztD7mWNKLSXci48m7cctATKfLlQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

  <script>
    // Função para iniciar o scanner e capturar o QR Code
    function onScanSuccess(decodedText, decodedResult) {
      // Quando um QR Code é detectado, mostramos a mensagem e enviamos o RM para o servidor
      document.getElementById('message').innerText = `QR Code detectado: ${decodedText}`;
      sendRMToServer(decodedText);
      // Stop the scanning after a QR code is successfully detected
      html5QrcodeScanner.stop();
    }

    // Função para tratar falhas no scanner
    function onScanError(errorMessage) {
      // Exemplo de uso: log ou exibir mensagens de erro, se necessário
      console.error(errorMessage);
    }

    // Enviar RM ao servidor via POST
    function sendRMToServer(rm) {
      fetch('registrar_entrada.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `rm=${rm}`
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          document.getElementById('message').innerText = `Entrada registrada para RM: ${rm}`;
        } else {
          document.getElementById('message').innerText = 'Erro ao registrar entrada.';
        }
      })
      .catch(error => {
        console.error('Erro ao comunicar com o servidor:', error);
        document.getElementById('message').innerText = 'Erro ao registrar entrada. Tente novamente.';
      });
    }

    // Inicialização do scanner
    function startScanner() {
      const config = {
        fps: 10, // Definindo frames por segundo (opcional)
        qrbox: 250, // Tamanho da área de leitura do QR Code
      };
      
      // Inicializa o scanner no elemento HTML com id "reader"
      const html5QrcodeScanner = new Html5QrcodeScanner("reader", config);
      
      // Inicia o scanner
      html5QrcodeScanner.render(onScanSuccess, onScanError);
    }

    // Inicia o scanner quando a página for carregada
    window.onload = startScanner;
  </script>
</body>
</html>


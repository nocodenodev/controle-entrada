<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Scanner de QR Code</title>
  <link rel="stylesheet" href="assets/css/styles.css">
  <style>
    .button {
      height: 55px;
      width: 100%;
      border-radius: 10px;
      color: #fff;
      font-size: 1rem;
      font-weight: 400;
      margin-top: 30px;
      border: none;
      cursor: pointer;
      transition: all 0.2s ease;
      background: rgb(130, 106, 251);
    }
    .button:hover {
      background: rgb(88, 56, 250);
    }
    .input-box {
      position: relative;
      height: 50px;
      width: 100%;
      outline: none;
      font-size: 1rem;
      color: #707070;
      margin-top: 8px;
      border: 1px solid #ddd;
      border-radius: 6px;
      padding: 0 15px;
    }
  </style>
</head>
<body>

  <div class="container">
    <div id="message">Posicione o QR Code à frente da câmera</div>
    <div id="reader"></div> <!-- Scanner vai ser renderizado aqui -->
    <div class="form">
      <div id="message">OU Digite o seu RM</div>
      <input class="input-box" type="text" id="rm" placeholder="RM">
      <button class="button" id="enviar-rm" onclick="sendRMToServer(document.getElementById('rm').value);">Enviar</button>
      <button class="button" id="enviar-rm" onclick="window.location.href='index.php';">Voltar</button>
    </div>
  </div>

  <!-- Inclusão da biblioteca -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.4/html5-qrcode.min.js" integrity="sha512-k/KAe4Yff9EUdYI5/IAHlwUswqeipP+Cp5qnrsUjTPCgl51La2/JhyyjNciztD7mWNKLSXci48m7cctATKfLlQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

  <script>
    let isProcessing = false; // Flag para garantir que o QR Code seja escaneado apenas uma vez

    // Função para iniciar o scanner e capturar o QR Code
    function onScanSuccess(decodedText, decodedResult) {
      if (isProcessing) return; // Impede o envio se já estiver processando

      // Quando um QR Code é detectado, mostramos a mensagem e enviamos o RM para o servidor
      document.getElementById('message').innerText = `QR Code detectado: ${decodedText}`;
      
      // Enviar RM ao servidor
      sendRMToServer(decodedText);
      isProcessing = true; // Define a flag para evitar o envio múltiplo
      html5QrcodeScanner.stop(); // Para o scanner após o sucesso
    }

    // Função para tratar falhas no scanner
    function onScanError(errorMessage) {
      // Falha silenciosa, sem console
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
          let inputRm = document.querySelector("#rm")
          let buttonRm = document.querySelector("#enviar-rm")
          inputRm.disabled = true;
          buttonRm.disabled = true;
          alert('Entrada registrada!')
        } else {
          document.getElementById('message').innerText = data.message; // Mensagem do servidor
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

<!DOCTYPE html>
<html>
<head>
  <title>Upload de Arquivo</title>
</head>
<body>

  <input type="file" id="arquivo" />
  <button id="enviar" >enviar</button>
  <button id='exportar'>Exportar</button>
  <div id="tabelaUsuarios" class="d-none" style="display:block">
      
      <table class="table table-bordered" stlye="display:none">
        <thead class="table-dark">
          <tr>
            <th>Nome</th>
            <th>Email</th>            
            <th>Idade</th>   
   
          </tr>
        </thead>
        <tbody id="corpoTabela">

        </tbody>
      </table>
    </div>
    
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script>
    $(document).ready(function () {
    $("#exportar").click(function () {
    let usuarios = []; 

    $('#tabelaUsuarios tbody tr').each(function () {
        let tds = $(this).find('td');

        let usuario = {
            nome: tds.eq(0).text().trim(),
            email: tds.eq(1).text().trim(),
            idade: tds.eq(2).text().trim()
        };
        usuarios.push(usuario);
    });

      $.ajax({
          url: './export.php',
          method: "POST",
          data: {
              users: usuarios
          },
          success: function(data) {
              const res = JSON.parse(data);
              const link = document.createElement('a');
              link.href = res.arquivo; 
              link.download = res.arquivo.split('/').pop();
              document.body.appendChild(link);
              link.click();
              document.body.removeChild(link);

              apagar_arquivo(res.arquivo);
          },
          async: true
      });    
    })
        

    $("#enviar").click(function(){
      let arquivo = $('#arquivo')[0].files[0];      
      if (arquivo) {
        

       let formData = new FormData();
        formData.append('arquivo', arquivo); 

        fetch('./export.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(result => {
         let html = '';
          
            result.forEach(item => {
              html += `<tr>
                          <td>${item[0]}</td>
                          <td>${item[1]}</td>
                          <td>${item[2]}</td>                          
                        </tr>`;
            });
            $('#corpoTabela').html(html);
            $('#tabelaUsuarios').removeClass('d-none');
        })
        .catch(error => {
          console.error('Erro ao enviar:', error);
        });
      }
    });
    function apagar_arquivo(name_arq){
      $.ajax({
        url:'./export.php',
        method:'POST',
        data: {
              name_arq  : name_arq,
              op: 'Apagar_arquivo'

        },success:function(data){
              console.log('arquivo apagado')             
            },
      })
    }
    });
    
    
  </script>

</body>
</html>

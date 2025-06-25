<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

$dados = $_POST['users']??null;
$aux = $_POST['op']??null;
$name_arq = $_POST['name_arq']??null;
if($aux =='Apagar_arquivo'){
     unlink($name_arq);
}else{
    if($dados){    

        //gera arquivo
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $colunas = array_keys($dados[0]);
        $rowNum = 1;
        //Cabeçalhos
        $prefixo = 'A';
        foreach($colunas as $coluna){
            $sheet->setCellValue($prefixo.$rowNum,$coluna);
            $prefixo++;
        }
        $rowNum++;

        foreach($dados as $linha){//pega todos valores
            $prefixoLinha = 'A';

            foreach($linha as $valor){//pega os valores por linnha       
                $sheet->setCellValue($prefixoLinha.$rowNum,$valor);
                $prefixoLinha++;
                        
            }
            $rowNum++; 
        }

        $data = date('Ymd_His');

        // Criando o writer e salvando o arquivo
        $writer = new Xlsx($spreadsheet);
        $name_arq = "Teste_" . $data . ".xlsx";
        $writer->save($name_arq);
        echo json_encode(['arquivo' => $name_arq]);
    }else{
        //grid

        if (isset($_FILES['arquivo'])) {
            $arquivo = $_FILES['arquivo'];
        }


        $arquivo = $_FILES['arquivo']['tmp_name'];

        // Carrega o arquivo
        $spreadsheet = IOFactory::load($arquivo);

        // Pega a primeira planilha
        $sheet = $spreadsheet->getActiveSheet();

        // Converte em array (inclusive com o cabeçalho)
        $dadosComCabecalho = $sheet->toArray();

        // Remove o cabeçalho (primeira linha)
        array_shift($dadosComCabecalho); // Remove a linha 0

        // Agora $dadosComCabecalho contém apenas os dados, sem os nomes das colunas
        echo json_encode($dadosComCabecalho);
    }
}
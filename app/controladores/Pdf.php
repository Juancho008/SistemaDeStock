<?php

use Fpdf\Fpdf;


// Clase principal PDF que usa MiPDF
class Pdf extends Controlador
{
    public function __construct($parametros = [])
    {
        parent::__construct();
        $this->usuario = $this->modelo("Usuario");
        $this->stock = $this->modelo("Stock");
        $this->producto = $this->modelo("Productos");
        $this->permiso = $this->modelo("Permiso");
        $this->pedidos = $this->modelo("Pedidos");
        $this->edificio = $this->modelo("Edificio");
        $this->estado = $this->modelo("Estado");
        $this->categorias = $this->modelo("Categorias");
        $this->detalles = $this->modelo("DetallesPedidos");
        $this->medidas = $this->modelo("Medidas");
        $this->sector = $this->modelo("Sector");
        $this->ordendia = $this->modelo("OrdenesDelDia");
        $this->area = $this->modelo("Area");
        $this->oficina = $this->modelo('Oficina');
        $this->actaBaja = $this->modelo('ActaBaja');
    }

    public function iniciar($parametros = [])
    {
        $orientacion = $parametros['Orientacion'] ?? 'P';
        $unidad = $parametros['Unidad'] ?? 'mm';
        $tamanio = $parametros['Tamanio'] ?? 'A4';

        $this->pdf = new MiPDF($orientacion, $unidad, $tamanio);
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->SetTextColor(80, 80, 80);
    }

    public function generarHoja()
    {
        $this->pdf->AddPage();
    }

    public function mostrar($nombrePdf = "Reporte de Suministro.pdf")
    {
        $this->pdf->Output("I", $nombrePdf);
    }

    public function imprimirStockDisponible()
    {
        $this->iniciar([
            "Orientacion" => "P",
            "Unidad" => "mm",
            "Tamanio" => "A4"
        ]);

        $this->pdf->SetAutoPageBreak(true, 20);
        $this->generarHoja();

        $this->pdf->SetY(17);
        $this->pdf->SetFont('Arial', 'B', 12);
        $this->pdf->Cell(0, 10, utf8_decode("Listado de Stock Disponible"), 0, 1, 'C');

        //Comienzo la cabezera
        $this->pdf->Ln(17);
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->SetFillColor(230, 230, 230); // Gris
        $this->pdf->Cell(20, 8, utf8_decode('#'), 1, 0, 'C', true);
        $this->pdf->Cell(60, 8, utf8_decode('Nombre producto'), 1, 0, 'C', true);
        $this->pdf->Cell(40, 8, utf8_decode('Categoría'), 1, 0, 'C', true);
        $this->pdf->Cell(40, 8, utf8_decode('Fecha Ingreso'), 1, 0, 'C', true);
        $this->pdf->Cell(30, 8, utf8_decode('Cantidad'), 1, 1, 'C', true);

        //Comienzo con la obtencion de datos
        $this->pdf->SetFont('Arial', '', 10);
        $productos = $this->producto->listar();
        if (!empty($productos)) {
            foreach ($productos as $producto) {
                $stocks = $this->stock->obtenerStockDisponiblePorIdProducto($producto['id']);
                $categoria = $this->categorias->obtenerCategoriaDisponible($producto['id_categoria']);
                // Evitar errores si no hay stock
                $cantidad = $stocks['cantidad'] ?? 0;

                $nombreCategoria = $categoria['nombre'] ?? 'null';
                $this->pdf->Cell(20, 8, $producto['id'], 1);
                $this->pdf->Cell(60, 8, utf8_decode($producto['nombre']), 1);
                $this->pdf->Cell(40, 8, utf8_decode($nombreCategoria), 1);
                $this->pdf->Cell(40, 8, date("d/m/Y", strtotime($producto['fecha_ingreso'] ?? '')), 1);
                $this->pdf->Cell(30, 8, $cantidad, 1, 1);
            }
        } else {
            $this->pdf->Cell(0, 10, 'No hay productos disponibles.', 1, 1, 'C');
        }

        $this->mostrar("StockDisponible.pdf");
    }


    public function imprimirStockDanado()
    {
        $meses = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        ];
        $numeroMes = date("n");
        $this->iniciar([
            "Orientacion" => "P",
            "Unidad" => "mm",
            "Tamanio" => "A4"
        ]);
        $this->pdf->SetAutoPageBreak(true, 20);
        $this->generarHoja();
        $this->pdf->SetY(17);
        $this->pdf->SetFont('Arial', 'B', 12);
        $contadorActaBaja = $this->actaBaja->obtenerActaBajaContador();
        $numero = $contadorActaBaja['contador'];
        $numeroActa = ($numero == 0) ? 1 : $numero + 1;
        $this->pdf->Cell(0, 10, utf8_decode("ACTA DE BAJA N° " . $numeroActa), 0, 1, 'C');
        // Texto descriptivo
        $this->pdf->Ln(20);
        $this->pdf->SetFont('Arial', '', 11);
        $texto = "En la ciudad de Posadas, capital de la Provincia de Misiones, a los ______________________ del mes " . $meses[$numeroMes] . " del " . date("Y") . ", los funcionarios intervinientes; Prosecretaria Administrativa, Jefe de Departamento Suministros han procedido a verificar los elementos que han quedado fuera del uso por rotura o deterioro en el Departamento Suministro, por las causas que en cada caso se indican:";
        $this->pdf->MultiCell(0, 8, utf8_decode($texto), 0, 'J');
        // Cabecera
        $this->pdf->Ln(15);
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->SetFillColor(230, 230, 230);
        $this->pdf->Cell(20, 8, utf8_decode('#'), 1, 0, 'C', true);
        $this->pdf->Cell(60, 8, utf8_decode('Nombre producto'), 1, 0, 'C', true);
        $this->pdf->Cell(40, 8, utf8_decode('Categoría'), 1, 0, 'C', true);
        $this->pdf->Cell(16, 8, utf8_decode('Cantidad'), 1, 0, 'C', true);
        $this->pdf->Cell(54, 8, utf8_decode('Motivo'), 1, 1, 'C', true);
        // Cuerpo
        $this->pdf->SetFont('Arial', '', 10);
        $productos = $this->producto->listar();
        if (!empty($productos)) {
            foreach ($productos as $producto) {
                $stocks = $this->stock->obtenerStockDanadoPorIdProducto($producto['id']);
                if (empty($stocks) || $stocks['estado'] != 1 || $stocks['cantidad'] <= 0) {
                    continue;
                }

                $categoria = $this->categorias->obtenerCategoriaDisponible($producto['id_categoria']);
                $cantidad = $stocks['cantidad'] ?? 0;
                $nombreCategoria = $categoria['nombre'] ?? 'null';
                $motivo = utf8_decode($stocks['observacion']);

                $x = $this->pdf->GetX();
                $y = $this->pdf->GetY();

                // Calcular altura basada en número de líneas
                $numLineas = $this->pdf->NbLines(54, $motivo);
                $altura = max($numLineas * 8, 8); // Ajusta si usas diferente altura de línea

                // Imprimir celdas
                $this->pdf->SetXY($x, $y);
                $this->pdf->Cell(20, $altura, $producto['id'], 1, 0);
                $this->pdf->Cell(60, $altura, utf8_decode($producto['nombre']), 1, 0);
                $this->pdf->Cell(40, $altura, utf8_decode($nombreCategoria), 1, 0);
                $this->pdf->Cell(16, $altura, $cantidad, 1, 0);
                $this->pdf->MultiCell(54, 8, $motivo, 1, 'L');

                $this->pdf->SetY($y + $altura);
            }
        } else {
            $this->pdf->Cell(0, 10, utf8_decode('No hay productos Dañado.'), 1, 1, 'C');
        }



        $tiempoActual = date("d/m/y");
        $retorno = ['fecha_impresion' => $tiempoActual];
        $this->actaBaja->guardar($retorno);
        $this->mostrar("ActaBaja.pdf");
    }



    public function imprimirStockTotal()
    {
        $this->iniciar([
            "Orientacion" => "P",
            "Unidad" => "mm",
            "Tamanio" => "A4"
        ]);

        $this->pdf->SetAutoPageBreak(true, 20);
        $this->generarHoja();

        $this->pdf->SetY(17);
        $this->pdf->SetFont('Arial', 'B', 12);
        $this->pdf->Cell(0, 10, utf8_decode("Listado de Stock"), 0, 1, 'C');

        //Comienzo la cabezera
        $this->pdf->Ln(17);
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->SetFillColor(230, 230, 230); // Gris
        $this->pdf->Cell(20, 8, utf8_decode('#'), 1, 0, 'C', true);
        $this->pdf->Cell(60, 8, utf8_decode('Nombre producto'), 1, 0, 'C', true);
        $this->pdf->Cell(40, 8, utf8_decode('Stock Disponible'), 1, 0, 'C', true);
        $this->pdf->Cell(40, 8, utf8_decode('Stock Dañado'), 1, 0, 'C', true);
        $this->pdf->Cell(30, 8, utf8_decode('Stock Total'), 1, 1, 'C', true);

        //Comienzo con la obtencion de datos
        $this->pdf->SetFont('Arial', '', 10);
        $productos = $this->producto->listar();
        if (!empty($productos)) {
            foreach ($productos as $producto) {
                $stockDanado = $this->stock->obtenerStockDanadoPorIdProducto($producto['id']);
                $stockDisponible = $this->stock->obtenerStockDisponiblePorIdProducto($producto['id']);
                $stocks['cantidad'] = $stockDanado['cantidad'] + $stockDisponible['cantidad'];
                // Evitar errores si no hay stock
                $cantidad = $stocks['cantidad'] ?? 0;
                $disponible = $stockDisponible['cantidad'];
                $danado = $stockDanado['cantidad'];
                $this->pdf->Cell(20, 8, $producto['id'], 1);
                $this->pdf->Cell(60, 8, utf8_decode($producto['nombre']), 1);
                $this->pdf->Cell(40, 8, $disponible, 1);
                $this->pdf->Cell(40, 8, $danado, 1);
                $this->pdf->Cell(30, 8, $cantidad, 1, 1);
            }
        } else {
            $this->pdf->Cell(0, 10, utf8_decode('No hay productos Dañado.'), 1, 1, 'C');
        }

        $this->mostrar("StockDanado.pdf");
    }
}

// Clase extendida(o herencia de su clase padre Fpdf) de FPDF para encabezado y pie automático
class MiPDF extends Fpdf
{
    function Header()
    {
        $this->Image('../public/img/logo-nuevo.png', 15, 2, 27);
        $this->Image('../public/img/iram.jpg', 165, 7, 27);


        $this->SetY(10);
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, utf8_decode("Sistema de Suministros"), 0, 1, 'C');

        $this->SetY(30);
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, utf8_decode("Fecha de Impresión: ") . date("d/m/Y - H:i"), 0, 1, 'L');
        $this->Ln(5);

        $this->SetY(30);
        $this->SetX(130);
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, utf8_decode("Fecha de Vigencia: 16/06/2025"), 0, 1, 'L');
        $this->Ln(5);

        $this->SetY(30);
        $this->SetX(185);
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, utf8_decode("Version: 02"), 0, 1, 'L');
        $this->Ln(5);

        $this->SetDrawColor(0, 0, 0); // Color negro
        $this->SetLineWidth(0.5); // Grosor de la línea (puedes cambiarlo)
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(5);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo(), 0, 0, 'R');
    }

    function NbLines($w, $txt)
    {
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }
}

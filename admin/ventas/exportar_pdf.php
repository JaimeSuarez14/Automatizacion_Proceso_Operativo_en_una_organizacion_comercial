<?php
require __DIR__ . '/../../db.php';
require __DIR__ . '/../../fpdf186/fpdf.php';

class PDF extends FPDF
{
  function Header()
  {
    $this->SetFont('Arial', 'B', 16);
    $this->Cell(0, 10, 'Reporte de Ventas', 0, 1, 'C');
    $this->Ln(5);
  }

  function Footer()
  {
    $this->SetY(-15);
    $this->SetFont('Arial', 'I', 8);
    $this->Cell(0, 10, 'Pagina ' . $this->PageNo(), 0, 0, 'C');
  }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 10);

// Encabezado tabla
$pdf->Cell(20, 8, 'ID', 1);
$pdf->Cell(40, 8, 'Cliente', 1);
$pdf->Cell(35, 8, 'Fecha', 1);
$pdf->Cell(30, 8, 'Monto', 1);
$pdf->Cell(35, 8, 'Estado', 1);
$pdf->Ln();

// Obtener datos
$stmt = $pdo->query("
    SELECT v.id_venta, v.fecha_venta, v.monto_total, v.estado, c.nombre
    FROM ventas v
    JOIN clientes c ON v.id_cliente = c.id_cliente
    ORDER BY v.fecha_venta DESC
    LIMIT 10
");

$ventas = $stmt->fetchAll();

$pdf->SetFont('Arial', '', 9);

foreach ($ventas as $v) {

  $pdf->Cell(20, 8, $v['id_venta'], 1);
  $pdf->Cell(40, 8, mb_convert_encoding($v['nombre'], 'ISO-8859-1', 'UTF-8'), 1);
  $pdf->Cell(35, 8, date('d/m/Y H:i', strtotime($v['fecha_venta'])), 1);
  $pdf->Cell(30, 8, 'S/. ' . number_format($v['monto_total'], 2), 1);
  $pdf->Cell(35, 8, mb_convert_encoding($v['estado'], 'ISO-8859-1', 'UTF-8'), 1);
  $pdf->Ln();
}

// Total
$total = array_sum(array_column($ventas, 'monto_total'));
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 10, 'Total: S/. ' . number_format($total, 2), 0, 1, 'R');

$pdf->Output('D', 'reporte_ventas.pdf');
exit;

<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoryReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Product::with(['category', 'brand'])
            ->orderBy('stock', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'SKU',
            'Product Name',
            'Category',
            'Brand',
            'Stock Quantity',
            'Base Price',
            'Sale Price',
            'Stock Value',
            'Status',
            'Last Updated'
        ];
    }

    public function map($product): array
    {
        return [
            $product->sku,
            $product->name,
            $product->category->name ?? 'N/A',
            $product->brand->name ?? 'N/A',
            $product->stock,
            $product->base_price,
            $product->sale_price ?? 'N/A',
            $product->stock * $product->base_price,
            ucfirst($product->status),
            $product->updated_at->format('Y-m-d H:i:s')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
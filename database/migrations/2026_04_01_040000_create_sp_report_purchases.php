<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Create stored procedure sp_report_purchases
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_report_purchases;
        ');

        DB::unprepared('
            CREATE PROCEDURE sp_report_purchases(
                IN p_start_date DATE,
                IN p_end_date DATE,
                IN p_product_id BIGINT UNSIGNED
            )
            BEGIN
                SELECT 
                    p.date as tanggal,
                    pr.name as nama_produk,
                    COUNT(DISTINCT p.id) as total_transaksi,
                    SUM(pi.qty) as total_qty,
                    SUM(pi.qty * pi.price) as total_amount
                FROM purchases p
                INNER JOIN purchase_items pi ON p.id = pi.purchase_id
                INNER JOIN products pr ON pi.product_id = pr.id
                WHERE p.date >= p_start_date 
                    AND p.date <= p_end_date
                    AND (p_product_id IS NULL OR pi.product_id = p_product_id)
                GROUP BY p.date, pr.id, pr.name
                ORDER BY p.date DESC;
            END
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_report_purchases;');
    }
};

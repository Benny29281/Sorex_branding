const express = require('express');
const router = express.Router();
const excel = require('exceljs'); // Package buat bikin file Excel
const Sales = require('./models/Sales'); // Asumsi lo punya model Sales

router.get('/download-excel', async (req, res) => {
    const { startDate, endDate, salesName } = req.query;

    try {
        // Filter data berdasarkan input user
        const filters = {};
        if (startDate && endDate) {
            filters.date = { $gte: new Date(startDate), $lte: new Date(endDate) };
        }
        if (salesName) {
            filters.salesName = salesName;
        }

        const salesData = await Sales.find(filters); // Ambil data dari DB

        // Generate file Excel
        const workbook = new excel.Workbook();
        const worksheet = workbook.addWorksheet('Sales Data');

        // Header Excel
        worksheet.columns = [
            { header: 'ID', key: '_id', width: 10 },
            { header: 'Nama Sales', key: 'salesName', width: 20 },
            { header: 'Tanggal', key: 'date', width: 15 },
            { header: 'Jumlah Penjualan', key: 'salesAmount', width: 15 }
        ];

        // Masukin data ke Excel
        salesData.forEach(sale => {
            worksheet.addRow({
                _id: sale._id,
                salesName: sale.salesName,
                date: sale.date.toISOString().split('T')[0], // Format ke tanggal aja
                salesAmount: sale.salesAmount
            });
        });

        // Kirim file Excel ke user
        res.setHeader(
            'Content-Type',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );
        res.setHeader(
            'Content-Disposition',
            'attachment; filename=sales_data.xlsx'
        );

        await workbook.xlsx.write(res);
        res.end();
    } catch (error) {
        res.status(500).json({ message: 'Error saat generate Excel', error });
    }
});

module.exports = router;

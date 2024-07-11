<?php//SAPInsert.php

include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();
//$resultArray = array();
//$arrCol = array();

$sql1 = "SELEC"
[
    {
        /* order header */
        "POSNumber": "DocEntry",
        "CardCode": "CardCode",
        "CardName": "CardName",
        "DocDate": "DocDate",
        "DocDueDate": "DocDueDate",
        "TaxDate": "DocDueDate",
        "NumAtCard": "",
        "CntctCode": 0,
        "SalesEmployee": SlpCode,
        "OwnerCode": 50, // ukey from eurox force
        "Comments": "Comments",
        "ShipToCode": "ShiptoCode",
        "PayToCode": "BilltoCode",
        "U_ShippingType": "ShippingType",
        "DiscSum": "DiscTotal", /* เพิ่ม */
        "Series": ??? /* เพิ่ม */

        /* เพิ่ม Series */
        /* เพิ่มส่วนลดท้ายบิล (บาท) */


        /* order detail */
        "Lines": [
            {
                "ItemCode": "ItemCode",
                "ItemDescription": "ItemName",
                "FreeText": "",
                "Quantity": Quantity,
                "ShipDate": "DocDueDate",
                "WhsCode": "WhsCode",
                "UnitPrice": UnitPrice,
                "VatGroup": "S07", /* TaxType FROM order_header */
                "UomCode": "Manual", /* GET FROM SAP */
                
                "U_Disct1": 12.1, /* Line_Disc1 */
                "U_Disct2": 13.1, /* Line_Disc2 */
                "U_Disct3": 14.1, /* Line_Disc3 */
                "U_Disct4": 15.1  /* Line_Disc4 */

                /* เพิ่ม BarCode และ/หรือ SubCatNum */
                /* เพิ่ม Disc. Amount ส่วนลดจำนวนเงิน เอามาจาก Line_Disc0 */
            },
            {
                "ItemCode": "01-001-010",
                "ItemDescription": "????�F10�KING_Test",
                "FreeText": "FreeText_Test",
                "Quantity": 20,
                "ShipDate": "2022-12-30",
                "WhsCode": "KSY",
                "UnitPrice": 10,
                "VatGroup": "S07",
                "UomCode": "Manual",
                "U_Disct1": 12.1,
                "U_Disct2": 13.1,
                "U_Disct3": 14.1,
                "U_Disct4": 15.1,
                "U_Disct5": 16.1
            }
        ]
    }
]

/* PRRRRRRRRRRRRRRRRRRRR */

[
    {
        "POSNumber": "DocEntry",
        "ReqType": 171,
        "Requester": "8", //ukey from eurox force
        "ReqName": "uName uLastName",
        "Series": 6305, // NNM1
        "DocDate": "DocDate",
        "DocDueDate": "DocDueDate",
        "TaxDate": "DocDueDate",
        "ReqDate": "DocDueDate",
        "OwnCode": 27, //ukey from eurox force
        "Comments": "Comments",
        "Lines": [
            {
                "ItemCode": "ItemCode",
                "ItemDescription": "ItemName",
                "Quantity": OpenQty,
                "ReqDate": "DocDueDate",
                "WhsCode": "WhsCode",
                "Unitprice": UnitPrice,
                "DiscPrcnt": 0, // ไม่ใช้ 
                "VatGroup": "P07",
                "LineTotal": 1070, // UnitPrice * OpenQty
                "UomCode": "Manual", /* GET FROM SAP */
                "LineVendor": "V-00090", // ไม่ใช้
                "FreeText": "????? Interface222" // ไม่ใช้
            },
            {
                "ItemCode": "ZZ-000-063",
                "ItemDescription": "????????? bosch 9.6V_222",
                "Quantity": 20,
                "ReqDate": "2022-12-08",
                "WhsCode": "KB1",
                "Unitprice": 20,
                "DiscPrcnt": 20,
                "VatGroup": "P07s",
                "LineTotal": 2140,
                "UomCode": "Kilogram",
                "LineVendor": "V-00128",
                "FreeText": "????? Interface222"
            }
        ]
    }
]
?>
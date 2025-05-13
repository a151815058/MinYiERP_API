<?php

function getPDOConnection() {
    $dsn = 'mysql:host=localhost;dbname=MinYiERP;charset=utf8mb4';
    $username = 'root';
    $password = 'QWe151815058~~~';

    try {
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die('資料庫連線失敗: ' . $e->getMessage());
    }
}
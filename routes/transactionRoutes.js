const express = require("express");
const router = express.Router();
const { createTransaction, paymentNotification, getTransactionByOrderId } = require("../controllers/transactionController");

router.post("/payment/create-transaction", createTransaction);
router.post("/payment/notification", paymentNotification);
router.get("/payment/:orderId", getTransactionByOrderId);

module.exports = router;

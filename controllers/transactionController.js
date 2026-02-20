
const midtransClient = require('midtrans-client');
const transporter = require('../helpers/MailHelper');
const { PrismaClient } = require('@prisma/client');
const prisma = new PrismaClient();

const snap = new midtransClient.Snap({
    isProduction: false,
    serverKey: process.env.MIDTRANS_SERVER_KEY,
    clientKey: process.env.MIDTRANS_CLIENT_KEY
});

const createTransaction = async (req, res) => {
    try {
        const { customerDetails, tripDetails } = req.body;

        const parameter = {
            transaction_details: {
                order_id: `ORDER-${Date.now()}`,
                gross_amount: tripDetails.price
            },
            credit_card: {
                secure: true
            },
            customer_details: {
                first_name: customerDetails.firstName,
                last_name: customerDetails.lastName,
                email: customerDetails.email,
                phone: customerDetails.phone
            },
            item_details: [
                {
                    id: tripDetails.id,
                    price: tripDetails.price,
                    quantity: 1,
                    name: tripDetails.name
                }
            ]
        };

        // Create transaction token
        const transaction = await snap.createTransaction(parameter);
        const transactionData = await prisma.transaction.create({
            data: {
                orderId: parameter.transaction_details.order_id,
                total: parameter.transaction_details.gross_amount,
                token: transaction.token,
                redirectUrl: transaction.redirect_url,
                name: `${customerDetails.firstName} ${customerDetails.lastName}`,
                email: customerDetails.email,
                total: parameter.transaction_details.gross_amount,
                status: 'pending'
            }
        });

        parameter.item_details.forEach(async (item) => {
            await prisma.transactionDetail.create({
                data: {
                    transactionId: transactionData.id,
                    price: item.price,
                    qty: item.quantity,
                    total: item.price * item.quantity,
                    name: item.name
                }
            });
        });

        transporter.sendMail({
            from: process.env.EMAIL_USER,
            to: customerDetails.email,
            subject: 'Transaction Confirmation',
            text: `Your transaction with order ID ${parameter.transaction_details.order_id} has been successfully processed. Check your transaction here ${transaction.redirect_url}`
        });
        
        res.json({
            success: true,
            token: transaction.token,
            redirect_url: transaction.redirect_url,
            orderId: parameter.transaction_details.order_id,
            transaction: transactionData,
            transactionDetails: parameter.item_details
        });
    } catch (error) {
        console.error('MIDTRANS_SERVER_KEY exists:', !!process.env.MIDTRANS_SERVER_KEY);
        console.error('MIDTRANS_SERVER_KEY length:', (process.env.MIDTRANS_SERVER_KEY || '').length);
        console.error('Midtrans Error:', error);
        res.status(500).json({
            success: false,
            message: error.message
        });
    }
}

const paymentNotification = async (req, res) => {
    try {
        const notification = req.body;
        
        // Verify notification
        const statusResponse = await snap.transaction.notification(notification);
        
        const orderId = statusResponse.order_id;
        const transactionStatus = statusResponse.transaction_status;
        const fraudStatus = statusResponse.fraud_status;

        console.log(`Transaction ${orderId} - Status: ${transactionStatus}`);

        // Handle transaction status
        if (transactionStatus == 'capture') {
            if (fraudStatus == 'accept') {
                await prisma.transaction.update({
                    where: {
                        orderId: orderId
                    },
                    data: {
                        status: 'success'
                    }
                });
            }
        } else if (transactionStatus == 'settlement') {
            await prisma.transaction.update({
                where: {
                    orderId: orderId
                },
                data: {
                    status: 'success'
                }
            });
        } else if (transactionStatus == 'cancel' || 
                   transactionStatus == 'deny' || 
                   transactionStatus == 'expire') {
            await prisma.transaction.update({
                where: {
                    orderId: orderId
                },
                data: {
                    status: transactionStatus
                }
            });
        } else if (transactionStatus == 'pending') {
            await prisma.transaction.update({
                where: {
                    orderId: orderId
                },
                data: {
                    status: transactionStatus
                }
            });
        }

        res.json({ success: true });
    } catch (error) {
        console.error('Notification Error:', error);
        res.status(500).json({ success: false });
    }
}

const getTransactionByOrderId = async (req, res) => {
    try {
        const { orderId } = req.params;
        const transaction = await prisma.transaction.findUnique({
            where: {
                orderId: orderId
            },
            include: {
                transactionDetails: true
            }
        });
        if (!transaction) {
            throw new Error("Transaction not found");
        }
        res.json({
            message: "Transaction found",
            data: transaction
        });
    } catch (error) {
        res.json({
            message: "Transaction not found",
            data: null
        });
    }
}

module.exports = {
    createTransaction,
    paymentNotification,
    getTransactionByOrderId
}
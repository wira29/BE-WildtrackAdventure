require('dotenv').config();

const express = require("express");
const app = express();
const cors = require('cors');

app.use(express.json());
app.use(express.urlencoded({ extended: true }));
app.use(cors({
    origin: 'http://localhost:5173', // Your React app URL
    credentials: true
}));


const authRoutes = require("./routes/AUTHrOUTES.JS");
const transactionRoutes = require("./routes/transactionRoutes");
const verifyToken = require("./middleware/authMiddleware");

app.use("/auth", authRoutes);
app.use("/transaction", transactionRoutes);

app.get('/profile', verifyToken, (req, res) => {
    res.json({
        message: "Profile success",
        data: req.user
    });
})

app.listen(3000, () => {
    console.log("Server is running on port 3000");
})

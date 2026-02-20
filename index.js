require('dotenv').config();

const express = require("express");
const app = express();
const cors = require('cors');

app.use(express.json());
app.use(express.urlencoded({ extended: true }));
const whitelist = [
  "https://www.wildtrack-adventure.com",
  "https://wildtrack-adventure.com",
  "http://localhost:5173",
  "http://localhost:3000"
];

app.use(cors({
  origin: function (origin, callback) {
    if (!origin || whitelist.includes(origin)) {
      callback(null, true);
    } else {
      callback(new Error("Not allowed by CORS"));
    }
  },
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

app.get('/', (req, res) => {
    res.json({
        message: "API running"
    });
})

app.listen(3000, () => {
    console.log("Server is running on port 3000");
})

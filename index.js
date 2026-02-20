process.on('uncaughtException', err => { console.error('Uncaught:', err); });
console.log('App started');

if (process.env.NODE_ENV !== 'production') {
 require('dotenv').config();
}
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

const authRoutes = require("./routes/authRoutes");
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

const PORT = process.env.PORT || 3000;

app.listen(PORT, () => {
  console.log("Server running on", PORT);
});

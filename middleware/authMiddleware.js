
const jwt = require("jsonwebtoken");

const verifyToken = (req, res, next) => {
    try {
        const authHeader = req.headers.authorization;
        if (!authHeader) {
            throw new Error("Authorization header is required");
        }
        const token = authHeader.split(" ")[1];
        if (!token) {
            throw new Error("Token is required");
        }

        const decoded = jwt.verify(token, process.env.JWT_SECRET);
        req.user = decoded.user;
        next();
    } catch (error) {
        res.json({
            message: "Invalid token",
            data: null
        });
    }
}

module.exports = verifyToken;
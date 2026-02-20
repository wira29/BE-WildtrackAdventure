
const jwt = require("jsonwebtoken");
const bcrypt = require("bcryptjs");
const { PrismaClient } = require('@prisma/client');
const prisma = new PrismaClient();

const login = async (req, res) => {
    try {
        const { email, password } = req.body;
        if (!email || !password) {
            throw new Error("Email and password are required");
        }
        const user = await prisma.user.findUnique({
            where: {
                email
            }
        });
        if (!user) {
            throw new Error("User not found");
        }
        const isPasswordValid = await bcrypt.compare(password, user.password);
        if (!isPasswordValid) {
            throw new Error("Invalid password");
        }
        const token = jwt.sign({ userId: user.id }, process.env.JWT_SECRET, {
            expiresIn: "1h"
        });
        res.json({
            message: "Login success",
            data: user,
            token
        });
    } catch (error) {
        res.json({
            message: "Login failed",
            data: null
        });
    }
}

const register = async (req, res) => {
    try {
        console.log(req.body);
        const { email, password, name } = req.body;
        if (!email || !password || !name) {
            throw new Error("Email, password, and name are required");
        }
        const hashedPassword = await bcrypt.hash(password, 10);
        const user = await prisma.user.create({
            data: {
                name,
                email,
                password: hashedPassword
            }
        });
        res.json({
            message: "Register success",
            data: user
        });
    } catch (error) {
        console.log(error);
        res.json({
            message: "Register failed",
            data: null,
            error: error
        });
    }
}

module.exports = {
    login, 
    register
}
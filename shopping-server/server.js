const express = require("express");
const app = express();
const PORT = 3000;

// Home route
app.get("/", (req, res) => {
  res.send("Welcome to My Online Store with free WiFi!");
});

// Products route
app.get("/products", (req, res) => {
  res.json([
    { id: 1, name: "Shoes", price: 1200 },
    { id: 2, name: "Backpack", price: 850 },
    { id: 3, name: "Watch", price: 950 },
    { id: 4, name: "Sunglasses", price: 600 },
    { id: 5, name: "Hat", price: 300 }
  ]);
});

// Start server
app.listen(PORT, () => {
  console.log(`Server running on http://localhost:${PORT}`);
});
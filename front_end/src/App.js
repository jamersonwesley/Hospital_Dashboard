import React, { useState } from "react";
import Dashboard from "./components/Dashboard";
import FinanceiroDashboard from "./components/FinanceiroDashboard";
import FarmaciaDashboard from "./components/FarmaciaDashboard";

function App() {
  const [view, setView] = useState("hospital");

  const botaoStyle = ativo => ({
    padding: "8px 16px",
    cursor: "pointer",
    border: "none",
    borderRadius: 4,
    background: ativo ? "#1976d2" : "#e0e0e0",
    color: ativo ? "#fff" : "#000",
    marginRight: 10
  });

  return (
    <div style={{ padding: 20 }}>

      <div style={{ marginBottom: 30 }}>
        <button
          style={botaoStyle(view === "hospital")}
          onClick={() => setView("hospital")}
        >
          Hospitalar
        </button>

        <button
          style={botaoStyle(view === "financeiro")}
          onClick={() => setView("financeiro")}
        >
          Financeiro
        </button>

        <button
          style={botaoStyle(view === "farmacia")}
          onClick={() => setView("farmacia")}
        >
          Farmácia
        </button>
      </div>

      {view === "hospital" && <Dashboard />}
      {view === "financeiro" && <FinanceiroDashboard />}
      {view === "farmacia" && <FarmaciaDashboard />}
    </div>
  );
}

export default App;

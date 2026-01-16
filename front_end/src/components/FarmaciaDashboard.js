import React, { useEffect, useState } from "react";
import axios from "axios";
import { Bar } from "react-chartjs-2";
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  BarElement,
  Tooltip,
  Legend
} from "chart.js";

ChartJS.register(CategoryScale, LinearScale, BarElement, Tooltip, Legend);

export default function FarmaciaDashboard() {
  const [dados, setDados] = useState({
    estoqueBaixo: [],
    proximosVencimento: [],
    curvaConsumo: []
  });

  useEffect(() => {
    axios
      .get("http://localhost:8000/api/farmacia")
      .then(res => {
        console.log("Dados da Farmácia:", res.data); 
        setDados(res.data);
      })
      .catch(err => console.error(err));
  }, []);

  // Dados do gráfico de curva de consumo
  const curvaConsumoData = {
    labels: (dados.curvaConsumo || []).map(m => m.nome),
    datasets: [
      {
        label: "Quantidade Prescrita",
        data: (dados.curvaConsumo || []).map(m => m.quantidade_prescrita),
        backgroundColor: "#FF6666" // vermelho claro
      },
      {
        label: "Quantidade Comprada",
        data: (dados.curvaConsumo || []).map(m => m.quantidade_comprada),
        backgroundColor: "#1976d2" // azul
      }
    ]
  };

  const calcularDiasRestantes = data_validade => {
    const hoje = new Date();
    const validade = new Date(data_validade);
    const diffTime = validade - hoje;
    return Math.ceil(diffTime / (1000 * 60 * 60 * 24)); // dias
  };

  return (
    <div style={{ padding: 20, maxWidth: 1100, margin: "auto" }}>
      <h1 style={{ textAlign: "center" }}>Dashboard Farmácia</h1>

      {/* Estoque Baixo */}
      <h3 style={{ marginTop: 20 }}>Medicamentos com Estoque Baixo</h3>
      <table width="100%" border="1" cellPadding="8">
        <thead>
          <tr>
            <th>Nome</th>
            <th>Estoque</th>
            <th>Estoque Mínimo</th>
          </tr>
        </thead>
        <tbody style={{ background: "#FF6666" }}>
          {(dados.estoqueBaixo || []).map(m => (
            <tr key={m.id}>
              <td>{m.nome}</td>
              <td>{m.estoque}</td>
              <td>{m.estoque_minimo}</td>
            </tr>
          ))}
          {(dados.estoqueBaixo || []).length === 0 && (
            <tr>
              <td colSpan="3" style={{ textAlign: "center" }}>
                Nenhum medicamento com estoque baixo
              </td>
            </tr>
          )}
        </tbody>
      </table>

      {/* Próximos do Vencimento */}
      <h3 style={{ marginTop: 30 }}>Medicamentos Próximos do Vencimento / Vencidos</h3>
      <table width="100%" border="1" cellPadding="8">
        <thead>
          <tr>
            <th>Nome</th>
            <th>Data de Vencimento</th>
            <th>Dias Restantes</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          {(dados.proximosVencimento || []).map(m => {
            const dias = calcularDiasRestantes(m.data_validade);
            const status = dias < 0 ? "Vencido" : "Próximo do vencimento";
            return (
              <tr key={m.id} style={{ color: dias < 0 ? "red" : "orange" }}>
                <td>{m.nome}</td>
                <td>{new Date(m.data_validade).toLocaleDateString()}</td>
                <td>{dias}</td>
                <td>{status}</td>
              </tr>
            );
          })}
          {(dados.proximosVencimento || []).length === 0 && (
            <tr>
              <td colSpan="4" style={{ textAlign: "center" }}>
                Nenhum medicamento próximo do vencimento
              </td>
            </tr>
          )}
        </tbody>
      </table>


      <h3 style={{ marginTop: 30 }}>Top 5 Medicamentos - Curva de Consumo (Últimos 3 meses)</h3>
      <div style={{ height: 350 }}>
        <Bar
          data={curvaConsumoData}
          options={{
            responsive: true,
            plugins: { legend: { position: "top" } },
            scales: { y: { beginAtZero: true } }
          }}
        />
      </div>
    </div>
  );
}

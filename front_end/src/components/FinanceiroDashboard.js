import React, { useEffect, useState } from "react";
import axios from "axios";
import { Bar, Doughnut } from "react-chartjs-2";
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  BarElement,
  ArcElement,
  Tooltip,
  Legend
} from "chart.js";

ChartJS.register(
  CategoryScale,
  LinearScale,
  BarElement,
  ArcElement,
  Tooltip,
  Legend
);


function formatarValor(valor) {
  const num = Number(valor || 0);

  if (num >= 1_000_000) {
    return (num / 1_000_000).toFixed(1).replace(".", ",") + " mi";
  }

  if (num >= 1_000) {
    return (num / 1_000).toFixed(1).replace(".", ",") + " mil";
  }

  return num.toFixed(2).replace(".", ",");
}

export default function FinanceiroDashboard() {
  const [dados, setDados] = useState(null);

  useEffect(() => {
    axios
      .get("http://localhost:8000/api/financeiro")
      .then(res => {
  
        setDados(res.data);
      })
      .catch(err => console.error(err));
  }, []);

  if (!dados) {
    return <p style={{ textAlign: "center" }}>Carregando...</p>;
  }

  
  const topMedicos = Array.isArray(dados.topMedicosProcedimentos)
    ? dados.topMedicosProcedimentos
    : [];

  const faturamentoConvenio = Array.isArray(dados.faturamentoConvenio)
    ? dados.faturamentoConvenio
    : [];

  const receitaProcedimentos = Number(dados.receitaProcedimentos || 0);
  const receitaDiarias = Number(dados.receitaDiarias || 0);
  const receitaTotal = receitaProcedimentos + receitaDiarias;


  const topMedicosData = {
    labels: topMedicos.map(m => m.medico),
    datasets: [
      {
        label: "Quantidade de Procedimentos",
        data: topMedicos.map(m => m.quantidade),
        backgroundColor: [
          "#1976d2",
          "#388e3c",
          "#f57c00",
          "#7b1fa2",
          "#c62828"
        ]
      }
    ]
  };

  const faturamentoConvenioData = {
    labels: faturamentoConvenio.map(c => c.convenio),
    datasets: [
      {
        data: faturamentoConvenio.map(c => c.total),
        backgroundColor: [
          "#4caf50",
          "#2196f3",
          "#ff9800",
          "#9c27b0"
        ]
      }
    ]
  };

  return (
    <div style={{ maxWidth: 1100, margin: "auto", padding: 20 }}>
      <h1 style={{ textAlign: "center" }}>
        Painel Financeiro e Produtividade
      </h1>


      <div
        style={{
          display: "flex",
          justifyContent: "space-around",
          margin: "40px 0"
        }}
      >
        <Receitas titulo="Receita Procedimentos" valor={receitaProcedimentos} />
        <Receitas titulo="Receita Diárias" valor={receitaDiarias} />
        <Receitas titulo="Receita Total" valor={receitaTotal} />
      </div>

      {/* ================== GRÁFICOS ================== */}
      <div style={{ display: "flex", gap: 40, justifyContent: "center" }}>
        <div style={{ width: 450 }}>
          <h3 style={{ textAlign: "center" }}>
            Top 5 Médicos por Procedimentos
          </h3>
          {topMedicos.length > 0 ? (
            <Bar data={topMedicosData} />
          ) : (
            <p style={{ textAlign: "center" }}>Sem dados</p>
          )}
        </div>

        <div style={{ width: 350 }}>
          <h3 style={{ textAlign: "center" }}>
            Faturamento por Convênio
          </h3>
          {faturamentoConvenio.length > 0 ? (
            <Doughnut
              data={faturamentoConvenioData}
              options={{
                plugins: {
                  tooltip: {
                    callbacks: {
                      label: function (context) {
                        const label = context.label || "";
                        const value = context.raw || 0;
                        return `${label}: R$ ${formatarValor(value)}`;
                      }
                    }
                  },
                  legend: {
                    position: "bottom",
                    labels: {
                      boxWidth: 14
                    }
                  }
                }
              }}
            />
          ) : (
            <p style={{ textAlign: "center" }}>Sem dados</p>
          )}
        </div>
      </div>

   
      <h3 style={{ marginTop: 50 }}>Top Médicos (Detalhado)</h3>

      <table width="100%" border="1" cellPadding="8">
        <thead>
          <tr>
            <th>Médico</th>
            <th>Quantidade</th>
          </tr>
        </thead>
        <tbody>
          {topMedicos.length > 0 ? (
            topMedicos.map((m, i) => (
              <tr key={i}>
                <td>{m.medico}</td>
                <td>{m.quantidade}</td>
              </tr>
            ))
          ) : (
            <tr>
              <td colSpan="2" style={{ textAlign: "center" }}>
                Nenhum dado encontrado
              </td>
            </tr>
          )}
        </tbody>
      </table>
    </div>
  );
}


function Receitas({ titulo, valor }) {
  return (
    <div
      style={{
        padding: 20,
        borderRadius: 8,
        background: "#f5f5f5",
        minWidth: 220,
        textAlign: "center"
      }}
    >
      <h3>{titulo}</h3>
      <h2>R$ {formatarValor(valor)}</h2>
    </div>
  );
}

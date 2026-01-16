import React, { useEffect, useState } from "react";
import axios from "axios";
import { Bar, Line } from "react-chartjs-2";
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  BarElement,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend
} from "chart.js";

ChartJS.register(
  CategoryScale,
  LinearScale,
  BarElement,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend
);

export default function Dashboard() {
  const [data, setData] = useState(null);
  const [pacienteNome, setPacienteNome] = useState("");
  const [medicoNome, setMedicoNome] = useState("");
  const [loading, setLoading] = useState(false);

  const carregarDados = async () => {
    setLoading(true);
    try {
      const response = await axios.get(
        "http://localhost:8000/api/ocupacao",
        {
          params: {
            paciente_nome: pacienteNome,
            medico_nome: medicoNome
          }
        }
      );
      setData(response.data);
    } catch (error) {
      console.error("Erro ao carregar dados:", error);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    carregarDados();
  }, []);

  if (!data) return <p style={{ textAlign: "center" }}>Carregando...</p>;


  const ocupacaoData = {
    labels: data.quartos.map(q => q.tipo),
    datasets: [
      {
        label: "Ocupados",
        data: data.quartos.map(q => q.ocupados),
        backgroundColor: "rgba(255, 99, 132, 0.7)"
      },
      {
        label: "Disponíveis",
        data: data.quartos.map(q => q.total - q.ocupados),
        backgroundColor: "rgba(75, 192, 192, 0.7)"
      }
    ]
  };

  const tempoData = {
    labels: data.tempoMedioInternacao.map(t => t.especialidade),
    datasets: [
      {
        label: "Tempo médio (dias)",
        data: data.tempoMedioInternacao.map(t =>
          Number(parseFloat(t.media_dias).toFixed(2))
        ),
        backgroundColor: "rgba(54, 162, 235, 0.7)"
      }
    ]
  };

  const historicoData = {
    labels: data.historico.map(h => h.mes),
    datasets: [
      {
        label: "Internações",
        data: data.historico.map(h => h.internacoes),
        borderColor: "rgba(255, 99, 132, 1)",
        backgroundColor: "rgba(255, 99, 132, 0.2)",
        fill: true,
        tension: 0.4
      },
      {
        label: "Altas",
        data: data.historico.map(h => h.altas),
        borderColor: "rgba(75, 192, 192, 1)",
        backgroundColor: "rgba(75, 192, 192, 0.2)",
        fill: true,
        tension: 0.4
      }
    ]
  };

  const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        position: "top"
      }
    },
    scales: {
      y: {
        beginAtZero: true
      }
    }
  };

  return (
    <div style={{ padding: 20, maxWidth: 1100, margin: "auto" }}>
      <h1 style={{ textAlign: "center" }}>Dashboard Hospitalar</h1>

      <h2 style={{ textAlign: "center" }}>
        Taxa de Ocupação Total: {data.taxaOcupacaoTotal}%
      </h2>


      <div
        style={{
          display: "flex",
          gap: 10,
          justifyContent: "center",
          margin: "30px 0"
        }}
      >
        <input
          type="text"
          placeholder="Nome do paciente"
          value={pacienteNome}
          onChange={e => setPacienteNome(e.target.value)}
          style={{ padding: 8, width: 200 }}
        />

        <input
          type="text"
          placeholder="Nome do médico"
          value={medicoNome}
          onChange={e => setMedicoNome(e.target.value)}
          style={{ padding: 8, width: 200 }}
        />

        <button
          onClick={carregarDados}
          style={{
            padding: "8px 20px",
            cursor: "pointer",
            background: "#1976d2",
            color: "#fff",
            border: "none",
            borderRadius: 4
          }}
        >
          Buscar
        </button>
      </div>

      <div style={{ height: 300, marginBottom: 50 }}>
        <h3 style={{ textAlign: "center" }}>Ocupação por Tipo de Quarto</h3>
        <Bar data={ocupacaoData} options={chartOptions} />
      </div>

      <div style={{ height: 300, marginBottom: 50 }}>
        <h3 style={{ textAlign: "center" }}>
          Tempo Médio de Internação por Especialidade
        </h3>
        <Bar data={tempoData} options={chartOptions} />
      </div>

      <div style={{ height: 300, marginBottom: 50 }}>
        <h3 style={{ textAlign: "center" }}>
          Internações vs Altas (Últimos Meses)
        </h3>
        <Line data={historicoData} options={chartOptions} />
      </div>

 
      <h3 style={{ textAlign: "center" }}>Histórico Detalhado</h3>

      <table
        style={{
          width: "100%",
          borderCollapse: "collapse",
          marginTop: 20
        }}
      >
        <thead>
          <tr style={{ background: "#eee" }}>
            <th>Paciente</th>
            <th>Tipo</th>
            <th>Data</th>
            <th>Médico</th>
            <th>Diagnóstico</th>
            <th>Observações</th>
          </tr>
        </thead>
        <tbody>
          {data.historicoPaciente.length === 0 ? (
            <tr>
              <td colSpan="6" style={{ textAlign: "center", padding: 20 }}>
                Nenhum registro encontrado
              </td>
            </tr>
          ) : (
            data.historicoPaciente.map((item, index) => (
              <tr key={index}>
                <td>{item.paciente}</td>
                <td>{item.tipo}</td>
                <td>{new Date(item.data).toLocaleString()}</td>
                <td>{item.medico}</td>
                <td>{item.diagnostico}</td>
                <td>{item.observacoes}</td>
              </tr>
            ))
          )}
        </tbody>
      </table>

      {loading && (
        <p style={{ textAlign: "center", marginTop: 20 }}>
          Atualizando dados...
        </p>
      )}
    </div>
  );
}

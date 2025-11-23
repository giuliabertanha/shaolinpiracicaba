-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 23/11/2025 às 10:08
-- Versão do servidor: 8.0.43
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `shaolin`
--

--
-- Estrutura para tabela `graduacoes`
--

CREATE TABLE `graduacoes` (
  `id` int NOT NULL,
  `id_modalidade` int NOT NULL,
  `nome` varchar(100) NOT NULL,
  `ordem` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Estrutura para tabela `matriculas`
--

CREATE TABLE `matriculas` (
  `cod` int NOT NULL,
  `id_modalidade` int NOT NULL,
  `id_usuario` int NOT NULL,
  `id_graduacao` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `usuario` char(30) DEFAULT NULL,
  `senha` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `nome` char(50) DEFAULT NULL,
  `telefone` char(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `email` char(50) DEFAULT NULL,
  `tipo` char(1) DEFAULT NULL,
  `admin` int DEFAULT NULL,
  `emb_ab` int NOT NULL,
  `emb_5anos` int NOT NULL,
  `emb_camp` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Estrutura para tabela `modalidades`
--

CREATE TABLE `modalidades` (
  `id` int NOT NULL,
  `nome` char(50) DEFAULT NULL,
  `id_professor1` int DEFAULT NULL,
  `id_professor2` int DEFAULT NULL,
  `id_professor3` int DEFAULT NULL,
  `id_professor4` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Índices de tabela `graduacoes`
--
ALTER TABLE `graduacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_modalidade` (`id_modalidade`);

--
-- Índices de tabela `matriculas`
--
ALTER TABLE `matriculas`
  ADD PRIMARY KEY (`cod`),
  ADD KEY `id_modalidade` (`id_modalidade`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_graduacao` (`id_graduacao`);

--
-- Índices de tabela `modalidades`
--
ALTER TABLE `modalidades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_professor1` (`id_professor1`),
  ADD KEY `id_professor2` (`id_professor2`),
  ADD KEY `id_professor3` (`id_professor3`),
  ADD KEY `id_professor4` (`id_professor4`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de tabela `graduacoes`
--
ALTER TABLE `graduacoes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `matriculas`
--
ALTER TABLE `matriculas`
  MODIFY `cod` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `modalidades`
--
ALTER TABLE `modalidades`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas `graduacoes`
--
ALTER TABLE `graduacoes`
  ADD CONSTRAINT `graduacoes_ibfk_1` FOREIGN KEY (`id_modalidade`) REFERENCES `modalidades` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `matriculas`
--
ALTER TABLE `matriculas`
  ADD CONSTRAINT `matriculas_ibfk_1` FOREIGN KEY (`id_modalidade`) REFERENCES `modalidades` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `matriculas_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `matriculas_ibfk_3` FOREIGN KEY (`id_graduacao`) REFERENCES `graduacoes` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `modalidades`
--
ALTER TABLE `modalidades`
  ADD CONSTRAINT `modalidades_ibfk_1` FOREIGN KEY (`id_professor1`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `modalidades_ibfk_2` FOREIGN KEY (`id_professor2`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `modalidades_ibfk_3` FOREIGN KEY (`id_professor3`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `modalidades_ibfk_4` FOREIGN KEY (`id_professor4`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

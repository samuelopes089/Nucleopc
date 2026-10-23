<?php
// Esta classe cuida dos dados usados nesta parte do sistema.
class Carrinho
{
    public function __construct()
    {
        if (!isset($_SESSION['carrinho'])) {
            $_SESSION['carrinho'] = [];
        }
    }

    public function adicionar(array $produto, int $quantidade): void
    {
        $id = $produto['id'];
        if (isset($_SESSION['carrinho'][$id])) {
            $_SESSION['carrinho'][$id]['quantidade'] += $quantidade;
        } else {
            $_SESSION['carrinho'][$id] = [
                'produto_id' => $produto['id'],
                'nome'       => $produto['nome'],
                'preco'      => $produto['preco'],
                'imagem'     => $produto['imagem'],
                'sku'        => $produto['sku'],
                'quantidade' => $quantidade,
            ];
        }
    }

    public function atualizarQuantidade(int $produtoId, int $quantidade): void
    {
        if ($quantidade <= 0) {
            $this->remover($produtoId);
            return;
        }
        if (isset($_SESSION['carrinho'][$produtoId])) {
            $_SESSION['carrinho'][$produtoId]['quantidade'] = $quantidade;
        }
    }

    public function remover(int $produtoId): void
    {
        unset($_SESSION['carrinho'][$produtoId]);
    }

    public function esvaziar(): void
    {
        $_SESSION['carrinho'] = [];
    }

    public function itens(): array
    {
        return $_SESSION['carrinho'] ?? [];
    }

    public function quantidadeTotal(): int
    {
        return array_sum(array_column($this->itens(), 'quantidade'));
    }

    public function total(): float
    {
        $total = 0;
        foreach ($this->itens() as $item) {
            $total += $item['preco'] * $item['quantidade'];
        }
        return $total;
    }
}

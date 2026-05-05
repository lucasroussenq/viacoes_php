<?php

declare(strict_types=1);

use App\Models\Viacao;

/** @var list<Viacao> $viacoesAtivas */

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="icon.png">
    <title>Quero Passagem</title>
    <link rel="stylesheet" href="home.css">
</head>
<body>

<header>
    <nav class="header-nav">
        <img alt="logo" src="media/logo_nova_grande.png" class="logo">
        <p><a href="#">Passagens</a></p>
        <p><a href="#">Novo!</a></p>
        <p><a href="#">Hotel</a></p>
        <button class="btn-login">Login</button>
        <button class="btn-ajuda">Ajuda</button>
        <div><img alt="ajuda" src="media/icon_atendimento-online_ajuda.svg"></div>
    </nav>
</header>

<main>

    <!-- BANNER -->
    <div class="banner-home"
         style="background-image: url('https://assets.queropassagem.com.br/public/Upload/fundo/home_fundo_1.jpg?1775650828')">
        <div class="banner2">
            <div class="search-box">
                <h1>Comprar passagem de ônibus</h1>
                <form id="search-form" class="form">
                    <div class="form-group">
                        <label for="origem">Origem</label>
                        <input id="origem" type="text" placeholder="Florianopolis, SC - Rodoviária">
                    </div>
                    <div class="form-group">
                        <label for="destino">Destino</label>
                        <input id="destino" type="text" placeholder="Curitiba, PR - Rodoviária">
                    </div>
                    <div class="form-group">
                        <label for="dataida">Data Ida</label>
                        <input id="dataida" type="date">
                    </div>
                    <div class="form-group">
                        <label for="datavolta">Data Volta</label>
                        <input id="datavolta" type="date">
                    </div>
                    <div>
                        <button type="submit" class="btn-primary">Buscar passagens</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- DESTAQUES -->
    <div class="container-cinza">
        <div class="comentario-item">
            <div class="comentario-icone">
                <i class="uil uil-trophy"></i>
            </div>
            <h3>Viagens seguras</h3>
            <p>Mais de 30 Milhões de compras</p>
        </div>
        <div class="comentario-item">
            <div class="comentario-icone">
                <i class="uil uil-trophy"></i>
            </div>
            <h3>Pagamento</h3>
            <p>Pague com PIX, Npay ou até em 12x</p>
        </div>
        <div class="comentario-item">
            <div class="comentario-icone">
                <i class="uil uil-trophy"></i>
            </div>
            <h3>Cancelamento</h3>
            <p>Passagens Flexíveis e atendimento personalizado</p>
        </div>
    </div>

    <!-- DESTINOS -->
    <section class="destinos">
        <h2>Escolha o seu destino</h2>
        <p class="destinos-subtitulo">São mais de 5mil destinos em todo o país para escolher sem sair de casa!</p>
        <div class="destinos-grid">
            <div class="destino-card">
                <img src="media/1a.jpg" alt="São Paulo">
                <h3>São Paulo</h3>
            </div>
            <div class="destino-card">
                <img src="media/57a.jpg" alt="Rio de Janeiro">
                <h3>Rio de Janeiro</h3>
            </div>
            <div class="destino-card">
                <img src="media/55a.jpg" alt="Curitiba">
                <h3>Curitiba</h3>
            </div>
            <div class="destino-card">
                <img src="media/64a.jpg" alt="Belo Horizonte">
                <h3>Belo Horizonte</h3>
            </div>
        </div>
    </section>

    <!-- VIAÇÕES -->
    <div class="viacoes">
        <h2>Passagens de Ônibus baratas: Viações de ônibus !!</h2>
        <div class="logo-viacoes">
            <?php if (empty($viacoesAtivas)): ?>
                <p>Nenhuma viação cadastrada ainda.</p>
            <?php endif; ?>
            <?php foreach ($viacoesAtivas as $v): ?>
                <a href="<?= htmlspecialchars($v->url ?? '#') ?>" target="_blank" rel="noopener"
                   title="<?= htmlspecialchars($v->nome) ?>">
                    <div class="wrapper">
                        <div class="box">
                            <?php if (!empty($v->logo)): ?>
                                <img src="./media/<?= htmlspecialchars($v->logo) ?>"
                                     alt="<?= htmlspecialchars($v->nome) ?>"
                                     width="110" height="44">
                            <?php else: ?>
                                <span><?= htmlspecialchars($v->nome) ?></span>
                            <?php endif; ?>
                        </div>
                        <h3 class="nome-viacoes"><?= htmlspecialchars($v->nome) ?></h3>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- TRECHOS MAIS PROCURADOS -->
    <div class="container_mais_procurados">
        <h2>Os trechos mais procurados em nossa Central de passagens:</h2>
        <div class="tabela-grid">
            <table>
                <thead>
                <tr>
                    <th scope="col">Partindo de:</th>
                    <th scope="col">Indo para:</th>
                </tr>
                </thead>
                <tbody>
                <tr><td>São Paulo</td><td>Rio de Janeiro</td></tr>
                <tr><td>Rio de Janeiro</td><td>São Paulo</td></tr>
                <tr><td>São Paulo</td><td>Curitiba</td></tr>
                <tr><td>Curitiba</td><td>São Paulo</td></tr>
                <tr><td>Brasília</td><td>Goiânia</td></tr>
                </tbody>
            </table>
            <table>
                <thead>
                <tr>
                    <th scope="col">Partindo de:</th>
                    <th scope="col">Indo para:</th>
                </tr>
                </thead>
                <tbody>
                <tr><td>Goiânia</td><td>Brasília</td></tr>
                <tr><td>São Paulo</td><td>Goiânia</td></tr>
                <tr><td>Belo Horizonte</td><td>São Paulo</td></tr>
                <tr><td>Goiânia</td><td>São Paulo</td></tr>
                <tr><td>São Paulo</td><td>Belo Horizonte</td></tr>
                </tbody>
            </table>
            <table>
                <thead>
                <tr>
                    <th scope="col">Partindo de:</th>
                    <th scope="col">Indo para:</th>
                </tr>
                </thead>
                <tbody>
                <tr><td>Florianópolis</td><td>Curitiba</td></tr>
                <tr><td>São Paulo</td><td>Londrina</td></tr>
                <tr><td>Porto Alegre</td><td>Curitiba</td></tr>
                <tr><td>Curitiba</td><td>Florianópolis</td></tr>
                <tr><td>São Paulo</td><td>Bauru</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- APP / QR CODE -->
    <section class="app">
        <div class="QRcode_banner">
            <img src="media/banner_download_app_2.png" alt="Baixe o app">
        </div>
    </section>

    <!-- PARCEIROS -->
    <div class="parceiros">
        <img src="media/parceiro.png" width="575" height="290" alt="Parceiros">
        <div class="parceiro-direita">
            <div class="parceiro_vantagens">
                <span class="parceiro-titulo">Agências de viagens</span><br>
                <span class="parceiro-texto">Sistema completo de emissão e venda de passagens rodoviárias para agências de viagens.</span>
            </div>
            <div class="parceiro_vantagens">
                <span class="parceiro-titulo">OTA's</span><br>
                <span class="parceiro-texto">Insira nosso banner (buscador de passagens) em seu site e ganhe comissões por cada venda.</span>
            </div>
        </div>
    </div>

    <!-- E-MAIL -->
    <div class="email-section">
        <form>
            <h2>Deseja receber e-mails com novidades e descontos exclusivos?</h2>
            <label for="nome">Nome:</label>
            <input type="text" id="nome" placeholder="Seu nome completo" required>
            <label for="email">E-mail:</label>
            <input type="email" id="email" placeholder="seuemail@gmail.com" required>
            <br><br>
            <input type="submit" value="Enviar">
        </form>
    </div>

    <!-- SOBRE + FAQ -->
    <div class="sobre">
        <div class="cards-grid">
            <div>
                <h4>Viajar de ônibus é rápido e fácil com a Quero Passagem</h4>
                <h2 class="texto">A Quero Passagem é o maior Portal de Passagens de Ônibus do Brasil - sua Central de
                    Passagens Rodoviárias online. Pesquise viações, compare horários, preços e compre passagens
                    rodoviárias sem sair de casa. São mais de 5 mil destinos em todo o país, conectando cidades como
                    Belo Horizonte, Curitiba, Brasília, São Paulo, Rio de Janeiro, Salvador, Goiânia e muito mais.
                </h2>
            </div>
            <div class="card">
                <div><img src="media/card_pagamento.png" alt="Pagamento"></div>
                Escolha a melhor forma de pagamento para você: compre sua passagem de ônibus em até 12x
                no cartão de crédito ou pague com débito, transferência bancária, boleto ou via Pix.
            </div>
            <div class="card">
                <div><img src="media/card_onibus.png" alt="Ônibus"></div>
                Viaje com conforto e segurança nas melhores companhias de ônibus do Brasil, como Viação
                Cometa, 1001, Catarinense, Itapemirim, Guanabara e outras 350 viações parceiras.
            </div>
            <div class="card">
                <div><img src="media/card_bilhetes.png" alt="Bilhetes"></div>
                Na Quero Passagem, você escolhe o horário, o assento e a empresa favorita para viajar.
                Finalize sua compra de passagem rodoviária online de forma rápida, segura e sem complicação.
            </div>
            <div class="card">
                <div><img src="media/card_praia.png" alt="Praia"></div>
                Confiança de quem já colocou mais de 15 milhões de passageiros na estrada. Compre sua
                passagem de ônibus em menos de 5 minutos e bora viajar tranquilo.
            </div>
        </div>

        <h2>Perguntas Frequentes</h2>

        <details>
            <summary>Quero Passagem é seguro para comprar passagens de ônibus online?</summary>
            <p>Sim! Comprar sua passagem pela Quero Passagem é seguro. A plataforma utiliza tecnologia de
                proteção de dados e pagamentos confiáveis para garantir que suas informações estejam sempre
                protegidas.</p>
        </details>
        <details>
            <summary>Quero Passagem é Confiável?</summary>
            <p>Sim! A Quero Passagem conecta você a diversas empresas de ônibus em todo o Brasil, permitindo
                comparar preços, horários e rotas para escolher a melhor opção.</p>
        </details>
        <details>
            <summary>Como fazer o cancelamento da minha passagem de ônibus?</summary>
            <p>Basta acessar Minha Conta, localizar sua passagem e seguir as orientações. O pedido deve ser
                feito antes do horário da viagem e segue as regras da empresa de ônibus.</p>
        </details>
        <details>
            <summary>Como e onde vou receber a confirmação de compra da minha passagem de ônibus?</summary>
            <p>Assim que o pagamento for aprovado, você recebe um e-mail com todos os detalhes da sua viagem,
                como dados da passagem, horário e orientações para o embarque.</p>
        </details>
        <details>
            <summary>Como alterar a data ou o horário da minha viagem de ônibus?</summary>
            <p>Basta acessar Minha Conta, encontrar sua passagem e solicitar a mudança. A alteração depende da
                disponibilidade de novos horários e das regras da empresa de ônibus.</p>
        </details>
        <details>
            <summary>Como usar o ID Jovem na reserva da passagem de ônibus?</summary>
            <p>Se você possui o ID Jovem, pode utilizar o benefício em viagens interestaduais. Para a compra, é
                necessário utilizar o link: https://queropassagem.com.br/gratuidade.</p>
        </details>
        <details>
            <summary>Qual é o melhor app para comprar passagens de ônibus?</summary>
            <p>Com o aplicativo da Quero Passagem você pode pesquisar destinos, comparar horários e comprar sua
                passagem diretamente pelo celular de forma rápida e segura.</p>
        </details>
        <details>
            <summary>Como comprar passagens de ônibus online?</summary>
            <p>Informe origem, destino e data; escolha o horário e a empresa; preencha os dados do passageiro e
                finalize o pagamento. A confirmação chegará por e-mail.</p>
        </details>
        <details>
            <summary>Qual é o telefone e whatsapp da Quero Passagem?</summary>
            <p>O número de WhatsApp da Quero Passagem é 11 4680-2994.</p>
        </details>
        <details>
            <summary>Quais são os canais de atendimento da Quero Passagem?</summary>
            <p>Você pode falar com a equipe de atendimento pelo chat no Minha Conta, e-mail ou WhatsApp.</p>
        </details>
        <details>
            <summary>Quanto tempo demora para confirmar a passagem de ônibus na Quero Passagem?</summary>
            <p>Normalmente a confirmação acontece logo após a aprovação do pagamento.</p>
        </details>
        <details>
            <summary>Quais as regras para viajar com animais de estimação?</summary>
            <p>As regras variam por empresa. Em geral, o pet deve estar em caixa de transporte adequada e com a
                documentação veterinária exigida.</p>
        </details>
        <details>
            <summary>Quais são os documentos necessários para embarcar no ônibus da rodoviária?</summary>
            <p>Basta apresentar um documento oficial e físico com foto, como RG, CNH ou passaporte.</p>
        </details>
        <details>
            <summary>Quais são os meios de pagamento aceitos na Quero Passagem?</summary>
            <p>São aceitos cartão de crédito, Pix, Boleto, Transferência Bancária, Carteira Digital e outras
                opções disponíveis no momento da compra.</p>
        </details>
        <details>
            <summary>Posso comprar passagens de ônibus para outras pessoas/terceiros?</summary>
            <p>Sim! Basta preencher os dados do passageiro que irá viajar no momento da compra.</p>
        </details>
        <details>
            <summary>Qual limite de peso e com quantas bagagens eu posso embarcar na minha viagem de ônibus?</summary>
            <p>Normalmente é permitido levar até 30 kg no bagageiro e até 5 kg de bagagem de mão, mas as regras
                podem variar dependendo da empresa de ônibus.</p>
        </details>
    </div>

</main>

<footer class="footer">
    <div class="tops">
        <div class="container_tops">
            <div class="coluna">
                <ul>
                    <h2>Top destinos</h2>
                    <li>Ônibus Rio de Janeiro</li>
                    <li>Ônibus São Paulo</li>
                    <li>Ônibus Brasília</li>
                    <li>Ônibus Campinas</li>
                    <li>Ônibus Londrina</li>
                    <li>+Destinos</li>
                </ul>
            </div>
            <div class="coluna">
                <ul>
                    <h2>Top Viações</h2>
                    <li>Passagens Cometa</li>
                    <li>Passagens Gontijo</li>
                    <li>Passagens 1001</li>
                    <li>Passagens Águia Branca</li>
                    <li>Passagens Pássaro Marron</li>
                    <li>+Viações</li>
                </ul>
            </div>
            <div class="coluna">
                <ul>
                    <h2>Top Rodoviárias</h2>
                    <li>Rodoviária São Paulo - Tietê</li>
                    <li>Rodoviária Rio de Janeiro - Novo Rio</li>
                    <li>Rodoviária Belo Horizonte - Gov. Israel Pinheiro (Tergip)</li>
                    <li>Rodoviária Curitiba</li>
                    <li>Rodoviária São Paulo - Barra Funda</li>
                    <li>+Rodoviárias</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="container_maior">
        <img src="media/logo_nova_grande.png" alt="logo">
        <p>Na Quero Passagem sua compra é totalmente segura!</p>
        <p>Para garantirmos que seus dados estejam sempre protegidos, não armazenamos nenhuma informação do cartão
            de crédito utilizado, seguindo os protocolos de criptografia e de segurança das principais instituições
            bancárias do Brasil.</p>
        <div class="lista_final">
            <ul>
                <li>Sobre Nós</li>
                <li>Termos de Uso</li>
                <li>Política de privacidade</li>
                <li>Termos de uso Lounge Vip</li>
                <li>Imprensa</li>
                <li>Minha Conta</li>
            </ul>
            <ul>
                <li>Atendimento Online</li>
                <li>Trabalhe Conosco</li>
                <li>Gratuidade</li>
                <li>Autoviações</li>
                <li>Rodoviárias</li>
                <li>Destinos</li>
            </ul>
            <ul>
                <li>Afiliados</li>
                <li>Versão Mobile</li>
                <li>Rodomilhas</li>
                <li>Viajo Mucho</li>
                <li>La Terminal Costa Rica</li>
            </ul>
        </div>
    </div>

    <div class="container_maior2">
        <div class="direita">
            <div>Siga nossas redes Sociais</div>
            <div class="sociais">
                <a href="https://www.instagram.com/queropassagem" target="_blank" aria-label="Instagram"></a>
                <i class="uil uil-linkedin"></i>
                <a href="https://www.youtube.com/channel/UCV9uFupk50da_JOeooO5KSA" target="_blank" aria-label="YouTube"></a>
                <a href="https://www.linkedin.com/company/queropassagem/" target="_blank" aria-label="LinkedIn"></a>
            </div>
        </div>
        <div class="esquerda"></div>
    </div>

    <div class="pagamento"></div>

    <div class="info">
        <p>Calçada das Margaridas, 163 - Sala 02 - Condomínio Centro Comercial Alphaville, Barueri - SP | CEP:
            06453-038 | CNPJ: 18.087.991/0001-57 | saconibus@queropassagem.com.br</p>
    </div>
    <div class="copyrigth">
        <p>Copyright 2026 © QueroPassagem.com.br</p>
    </div>
    <p class="assinatura">"Vanessa esteve aqui :p"</p>
</footer>

</body>
</html>
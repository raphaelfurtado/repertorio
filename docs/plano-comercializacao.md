# Plano de Comercialização — Repertorio Pro

> App de **setlist + palco** para músicos (foco Brasil).
> Documento de estratégia: prontidão, foco, valor/preço e marketing.

---

## TL;DR

- Hoje o app é um ótimo **núcleo** (setlist por blocos, tom/BPM, modo palco, link pra banda), mas **ainda não está pronto pra cobrar**.
- Faltam 3 pré-requisitos pra monetizar com confiança: **letra/cifra completa**, **offline (PWA)** e **cobrança (PIX)**.
- **Posicionamento:** NÃO seja Cifra Club. Seja a **ferramenta de setlist + palco + banda na mesma página**.
- **Modelo:** freemium. Pro a **R$ 19,90/mês** ou **R$ 149/ano**.
- **Maior risco:** construir demais antes de validar. Confirme que ~10 pessoas pagam **antes** de polir.

---

## 1. Prontidão — o que falta pra cobrar

O app guarda **metadados** da música (título, artista, tom, BPM, intro) + organização de setlist + modo palco + link público. Núcleo bom, mas faltam:

- [ ] **Letra/cifra completa** — hoje não dá pra ler a música, só a "ficha". No palco o músico precisa do conteúdo.
- [ ] **Funcionar offline (PWA)** — bar/casa de show tem wi-fi ruim. Travou uma vez no show, ele desinstala. Quase obrigatório.
- [ ] **Cobrança** — não existe assinatura. No Brasil, gateway com **PIX** (Mercado Pago, Asaas, Pagar.me ou Stripe BR). PIX anual à vista reduz churn e gera caixa.
- [ ] **Termos de Uso e Política de Privacidade reais** (a tela de login já cita, mas não existem).

> ⚠️ **Não tente virar Cifra Club.** Base de cifras é um moat de anos de conteúdo. Seu diferencial é a **organização de setlist + experiência de palco + banda junto**.

---

## 2. Foco — o "wedge"

Dobrar a aposta no que o app já faz de melhor e mirar **um nicho específico**.

### ICP (cliente ideal)
- **Líder de banda cover** no Brasil — pagode, sertanejo, MPB, rock cover.
  - Gerencia 200–500 músicas.
  - Monta o set por **bloco** (o app já modela isso).
  - Recebe pedido e precisa de **tom e BPM na hora**.
  - Caso de uso central: **levar o repertório no palco**.
- Segundo perfil: **voz-e-violão** (solo).

### O que constrói confiança pra esse cliente pagar
**Offline + letra/cifra + transposição de tom.**
(É exatamente o roadmap técnico que já vínhamos discutindo — não é coincidência, é o caminho da monetização.)

---

## 3. Valor e preço

Modelo **freemium** — deixa entrar de graça, cobra no que vicia.

| Plano | O que tem | Preço sugerido (BR) |
|---|---|---|
| **Free** | 1–2 repertórios, nº limitado de músicas | R$ 0 |
| **Pro** | Repertórios ilimitados, **offline**, modo palco, transposição, compartilhar com a banda | **R$ 19,90/mês** ou **R$ 149/ano** |
| **Banda** | Vários membros editam, papéis/permissões | R$ 39,90/mês |

**Racional:** músico que tira R$ 250–500 por show paga tranquilo R$ 15–20/mês por algo que economiza tempo e evita micão no palco.

**Ancore o valor na dor, não na feature:**
- "Nunca mais erre o tom no palco."
- "Monte o set em 2 minutos."
- "A banda toda na mesma página."

**Dica:** lance um **plano fundador** (ex.: R$ 99/ano pros 100 primeiros). Valida disposição a pagar e gera caixa inicial.

---

## 4. Marketing / Go-to-Market

Músico brasileiro vive em **Instagram, TikTok, YouTube e grupos de WhatsApp**.

- **Loop viral embutido (grátis, já existe):** todo link público expõe o app. O rodapé da tela pública já diz *"Compartilhado via Repertorio Pro"* — transformar em **CTA clicável**: "Crie o seu repertório grátis". Quem vê a tela no palco também vira lead.
- **Conteúdo (reels curtos):**
  - "Como organizo 300 músicas pra show"
  - Demo do **modo palco**
  - Demo de **transposição em 1 toque**
  - "Monte um set de pagode em 2 minutos"
- **Seeding de influência:** dar Pro grátis pra 10–20 músicos cover de médio porte em troca de 1 reel/story. Barato, ROI alto.
- **Parcerias:** escolas de música, agências que contratam bandas, lojas de instrumento, grupos de Facebook/Telegram de "músicos profissionais", contratantes de eventos.
- **Presencial/direto:** ir aos bares com música ao vivo, falar com a banda no intervalo (feedback + primeiros usuários).
- **SEO:** "app de setlist", "como montar repertório de show", "tom e cifra para banda".

---

## 5. Plano 30 / 60 / 90 dias

### 0–30 dias — Validar ANTES de construir
- Colocar 15–20 músicos gigando pra usar de verdade; observar uso.
- Perguntar diretamente: "pagaria por isso?".
- Pré-vender o **plano fundador**.
- Instalar um **analytics simples** pra saber o que é usado.

### 30–60 dias — MVP pago
- **Offline (PWA)**.
- **Letra/cifra + transposição**.
- **Cobrança com PIX**.
- **Termos de Uso + Política de Privacidade** reais.

### 60–90 dias — Crescer
- Ativar o **loop viral** no link público (CTA).
- Começar os **reels** + **seeding de influência**.
- Lançar o **plano Banda**.

---

## 6. Riscos e cuidados

- ⚠️ **Maior risco:** construir muito antes de validar. Confirme ~10 pagantes antes de polir demais.
- **Operacional p/ cobrar no Brasil:** MEI/CNPJ, gateway com PIX, política de reembolso.
- **Confiabilidade no palco** é inegociável — offline e estabilidade vêm antes de qualquer "feature bonita".
- **Métrica-norte:** nº de músicos que montam um setlist e **voltam pra usar num show real** (retenção), não cadastros.

---

## 7. Próximos passos técnicos (sugeridos)

Ordem recomendada pra destravar monetização:

1. **PWA / offline** — confiabilidade no palco.
2. **Letra/cifra + transposição** — conteúdo que faz pagar.
3. **Pagamento com PIX** — assinatura Free/Pro/Banda.
4. **Termos + Privacidade + analytics**.

> Cada item pode virar uma branch separada para teste.

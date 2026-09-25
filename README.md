# 🤖  RAG AI Chat System

A Retrieval Augmented Generation (RAG) based AI chatbot system built with PHP, Ollama, Qdrant Vector Database and Local LLM models.

This project creates an intelligent Persian AI assistant that can answer user questions based on private knowledge stored in a vector database.

---

# 📌 What is RAG?

RAG (Retrieval Augmented Generation) is an AI architecture that combines:

1. **Information Retrieval**
2. **Large Language Model Generation**

Instead of asking an AI model to answer only from its internal knowledge, RAG first searches relevant information from an external database and then provides that information to the AI model.

The workflow:

```
User Question
      |
      |
      v
Generate Embedding
      |
      |
      v
Vector Database Search
      |
      |
      v
Retrieve Relevant Documents
      |
      |
      v
Send Context + Question to LLM
      |
      |
      v
AI Generated Answer
```

---

# 🏗 System Architecture

```
                 User
                  |
                  |
                  v
          PHP Backend API
                  |
      -------------------------
      |                       |
      v                       v

 Ollama Embed API        Qdrant Vector DB
      |                       |
      |                       |
      v                       v

 Text Embedding       Similar Documents

              |
              |
              v

          Ollama Chat API

              |
              |
              v

        Final AI Response
```

---

# 🧩 Technologies

| Technology | Usage |
|-|-|
| PHP | Backend API |
| Guzzle HTTP | HTTP Client |
| Ollama | Local AI Model Runtime |
| nomic-embed-text | Embedding Model |
| qwen2.5-coder | Language Model |
| Qdrant | Vector Database |

---

# 🔥 How RAG Works In This Project

## 1. User Sends Message

The frontend sends conversation history:

Example:

```json
{
 "messages":[
    {
      "role":"user",
      "content":"RAG چیست؟"
    }
 ]
}
```

---

# 2. Generate Embedding

The user question is converted into a numerical vector.

Example:

```
"RAG چیست؟"

        ↓

[0.023,0.532,-0.123,...]
```

This vector represents the semantic meaning of the text.

The project uses Ollama embedding API:

```
POST

http://127.0.0.1:11434/api/embed
```

Request:

```json
{
 "model":"nomic-embed-text:latest",
 "input":"RAG چیست؟"
}
```

Response:

```json
{
 "embeddings":[
    [
      0.023,
      0.532,
      -0.123
    ]
 ]
}
```

---

# 3. Vector Search Using Qdrant

After generating the embedding, the system searches the vector database.

API:

```
POST

http://127.0.0.1:6333/collections/ai-chat/points/search
```

Request:

```json
{
 "vector":[
    0.023,
    0.532,
    -0.123
 ],
 "limit":3,
 "with_payload":true
}
```

Qdrant compares the question vector with stored document vectors and returns the most similar information.

Example result:

```json
{
"text":"RAG combines retrieval and generation..."
}
```

---

# 4. Building AI Context

The backend combines:

- Retrieved documents
- Previous conversation history
- Current user question

Example:

```
Useful Information:

RAG combines retrieval and generation.

Conversation:

User: Explain AI

Main Question:

What is RAG?
```

This complete context is sent to the LLM.

---

# 5. Generate Final Answer

The project uses Ollama Chat API:

```
POST

http://127.0.0.1:11434/api/chat
```

Model:

```
qwen2.5-coder:7b
```

Request:

```json
{
"model":"qwen2.5-coder:7b",

"messages":[
 {
  "role":"system",
  "content":"You are a Persian AI assistant"
 },
 {
  "role":"user",
  "content":"context + question"
 }
],

"stream":false
}
```

The model generates the final Persian response.

---

# 📂 PHP API Explanation

## Import Dependencies



Loads Composer dependencies.

The project uses:

```
guzzlehttp/guzzle
```

for HTTP communication.


---

# Create HTTP Client

```php
$client = new \GuzzleHttp\Client();
```

Creates a client for calling external AI services.


---

# Generate Embedding

```php
$request=$client->post(
"http://127.0.0.1:11434/api/embed"
)
```

Sends the user question to Ollama embedding model.

Model:

```
nomic-embed-text:latest
```

Purpose:

Convert text into vector representation.

---

# Search Similar Documents

```php
$request=$client->post(
"http://127.0.0.1:6333/collections/ai-chat/points/search"
)
```

Searches Qdrant collection:

```
ai-chat
```

Returns top 3 similar documents:

```php
"limit"=>3
```

---

# Prepare Prompt

The system creates a new prompt containing:

```
Retrieved Knowledge

+

Chat History

+

User Question
```

This is the core RAG process.

---

# Send To AI Model

```php
http://127.0.0.1:11434/api/chat
```

Model:

```
qwen2.5-coder:7b
```

The system prompt forces:

- Persian answers
- Friendly conversation
- No hallucination
- Correct tool usage

---

# Return Response

Final output:

```json
{
 "message":
 "پاسخ تولید شده توسط هوش مصنوعی"
}
```

---

# 🚀 Installation

## 1. Install Ollama

Download:

https://ollama.com


Install models:

```bash
ollama pull nomic-embed-text

ollama pull qwen2.5-coder:7b
```

Run Ollama:

```bash
ollama serve
```

---

## 2. Install Qdrant

Using Docker:

```bash
docker run -p 6333:6333 qdrant/qdrant
```

---

## 3. Install PHP Dependencies

```bash
composer install
```

---

# 📡 API Usage

Endpoint:

```
POST /chat.php
```

Request:

```json
{
 "messages":[
  {
   "role":"user",
   "content":"RAG چیست؟"
  }
 ]
}
```

Response:

```json
{
 "message":
 "RAG یک معماری هوش مصنوعی است..."
}
```

---

# 🔐 Advantages

✅ Runs locally  
✅ No external AI API cost  
✅ Supports private knowledge bases  
✅ Persian language optimized  
✅ Fast semantic search  
✅ Easy to extend  

---




# 🤖 سامانه چت هوش مصنوعی مبتنی بر RAG

یک سیستم چت‌بات هوش مصنوعی فارسی مبتنی بر معماری **Retrieval Augmented Generation (RAG)** که با استفاده از PHP، Ollama، Qdrant و مدل‌های زبانی محلی ساخته شده است.

این پروژه یک دستیار هوش مصنوعی ایجاد می‌کند که قبل از پاسخ‌دهی، اطلاعات مرتبط را از یک پایگاه دانش جستجو کرده و سپس با کمک مدل زبانی، پاسخ دقیق‌تری تولید می‌کند.

---

# 📌 RAG چیست؟

**RAG (تولید تقویت‌شده با بازیابی اطلاعات)** یک معماری هوش مصنوعی است که دو بخش اصلی را ترکیب می‌کند:

1. **بازیابی اطلاعات (Retrieval)**
2. **تولید پاسخ توسط مدل زبانی (Generation)**

در مدل‌های معمولی هوش مصنوعی، مدل فقط بر اساس اطلاعاتی که هنگام آموزش یاد گرفته پاسخ می‌دهد.

اما در معماری RAG، ابتدا اطلاعات مرتبط از یک منبع خارجی مانند دیتابیس، فایل‌ها یا پایگاه دانش پیدا می‌شود و سپس این اطلاعات به مدل هوش مصنوعی داده می‌شود تا پاسخ دقیق‌تر و مرتبط‌تری تولید کند.

---

# ⚙️ روند کار سیستم

```
              سوال کاربر
                  |
                  |
                  v

          تبدیل متن به Embedding

                  |
                  |
                  v

       جستجو در دیتابیس برداری Qdrant

                  |
                  |
                  v

       دریافت اطلاعات مرتبط

                  |
                  |
                  v

      ارسال اطلاعات + سوال به مدل AI

                  |
                  |
                  v

          تولید پاسخ نهایی
```

---

# 🏗 معماری سیستم

```
                 کاربر

                   |
                   |

             PHP Backend API

                   |
       ---------------------------
       |                         |

       v                         v

 Ollama Embedding API       Qdrant Database

       |                         |

       |                         |

       v                         v

 تبدیل متن به بردار       جستجوی اطلاعات مشابه


              |
              |
              v


          Ollama Chat API

              |
              |
              v

        پاسخ نهایی هوش مصنوعی
```

---

# 🧩 تکنولوژی‌های استفاده شده

| تکنولوژی | کاربرد |
|---|---|
| PHP | توسعه API اصلی |
| Guzzle HTTP | ارسال درخواست‌های HTTP |
| Ollama | اجرای مدل‌های هوش مصنوعی به صورت Local |
| nomic-embed-text | ساخت Embedding متن |
| qwen2.5-coder:7b | مدل تولید پاسخ |
| Qdrant | دیتابیس برداری |

---

# 🧠 معماری RAG در این پروژه

## مرحله اول: دریافت سوال کاربر

فرانت‌اند پیام‌های کاربر را به API ارسال می‌کند.

نمونه درخواست:

```json
{
 "messages":[
    {
      "role":"user",
      "content":"RAG چیست؟"
    }
 ]
}
```

---

# مرحله دوم: ساخت Embedding

ابتدا سوال کاربر به یک بردار عددی تبدیل می‌شود.

مثلا:

```
RAG چیست؟

        ↓

[
0.234,
0.543,
-0.123,
...
]
```

این بردار مفهوم معنایی متن را نمایش می‌دهد.

برای این کار از API مربوط به Ollama استفاده می‌شود:

```
POST

http://127.0.0.1:11434/api/embed
```

---

## درخواست Embedding

```json
{
 "model":"nomic-embed-text:latest",
 "input":"RAG چیست؟"
}
```

---

## پاسخ Embedding

```json
{
 "embeddings":[
    [
      0.234,
      0.543,
      -0.123
    ]
 ]
}
```

---

# مرحله سوم: جستجو در Qdrant

بعد از ایجاد بردار، سیستم به دیتابیس برداری Qdrant درخواست ارسال می‌کند.

هدف:

پیدا کردن متن‌هایی که از نظر معنایی بیشترین شباهت را با سوال کاربر دارند.

API:

```
POST

http://127.0.0.1:6333/collections/ai-chat/points/search
```

---

## درخواست جستجو

```json
{
 "vector":[
    0.234,
    0.543,
    -0.123
 ],

 "limit":3,

 "with_payload":true
}
```

---

پارامترها:

### vector

بردار تولید شده از سوال کاربر.

### limit

تعداد نتایج مشابه.

در این پروژه:

```json
"limit":3
```

یعنی ۳ نتیجه برتر دریافت می‌شود.

### with_payload

باعث می‌شود متن اصلی ذخیره شده نیز برگردانده شود.

---

# مرحله چهارم: ساخت Context برای مدل AI

بعد از دریافت اطلاعات از Qdrant، سیستم سه بخش را ترکیب می‌کند:

```
1- اطلاعات پیدا شده از دیتابیس

+

2- تاریخچه گفتگو

+

3- سوال اصلی کاربر
```

نمونه:

```
اطلاعات کمکی:

RAG ترکیبی از بازیابی اطلاعات و تولید پاسخ است.


تاریخچه گفتگو:

کاربر: هوش مصنوعی چیست؟


سوال اصلی:

RAG چیست؟
```

---

# مرحله پنجم: ارسال به مدل زبانی

اطلاعات آماده شده به مدل زبانی ارسال می‌شود.

API:

```
POST

http://127.0.0.1:11434/api/chat
```

مدل استفاده شده:

```
qwen2.5-coder:7b
```

---

## درخواست Chat API

```json
{
 "model":"qwen2.5-coder:7b",

 "messages":[

 {
  "role":"system",
  "content":"تو یک دستیار فارسی زبان هستی"
 },

 {
  "role":"user",
  "content":"اطلاعات بازیابی شده + سوال کاربر"
 }

 ],

 "stream":false
}
```

---

# توضیح کد PHP



کتابخانه‌های PHP را بارگذاری می‌کند.

در این پروژه از:

```
Guzzle HTTP Client
```

برای ارتباط با APIها استفاده شده است.



---

# بررسی اعتبار درخواست

کد بررسی می‌کند که:

- درخواست خالی نباشد
- آرایه messages وجود داشته باشد
- حداقل یک پیام ارسال شده باشد

در صورت مشکل:

```json
{
 "error":"Invalid request"
}
```

برگردانده می‌شود.

---

# ساخت Embedding

کد:

```php
$client->post(
"http://127.0.0.1:11434/api/embed"
)
```

سوال کاربر را به مدل:

```
nomic-embed-text:latest
```

ارسال می‌کند.

خروجی:

یک بردار عددی است.

---

# جستجوی اطلاعات مرتبط

کد:

```php
$client->post(
"http://127.0.0.1:6333/collections/ai-chat/points/search"
)
```

در مجموعه:

```
ai-chat
```

جستجو انجام می‌دهد.

---

# آماده سازی Prompt

اطلاعات پیدا شده:

```php
$result->payload->text
```

به همراه تاریخچه گفتگو به یک متن واحد تبدیل می‌شود.

این متن به مدل AI داده می‌شود.

---

# ارسال به مدل زبانی

کد:

```php
/api/chat
```

در Ollama اجرا می‌شود.

مدل:

```
qwen2.5-coder:7b
```

وظیفه مدل:

- تحلیل اطلاعات
- پاسخ فارسی
- حفظ تاریخچه گفتگو
- جلوگیری از ساخت اطلاعات غیرواقعی

---

# خروجی API

در پایان:

```json
{
 "message":
 "پاسخ تولید شده توسط هوش مصنوعی"
}
```

به کاربر ارسال می‌شود.

---

# 🚀 نصب و اجرا

## نصب Ollama

ابتدا Ollama را نصب کنید:

```
https://ollama.com
```

---

## دانلود مدل‌ها

مدل Embedding:

```bash
ollama pull nomic-embed-text
```

مدل گفتگو:

```bash
ollama pull qwen2.5-coder:7b
```

اجرای Ollama:

```bash
ollama serve
```

---

# نصب Qdrant

با Docker:

```bash
docker run -p 6333:6333 qdrant/qdrant
```

---

# نصب وابستگی‌های PHP

```bash
composer install
```

---

# 📡 استفاده از API

آدرس:

```
POST /embed.php
```

نمونه درخواست:

```json
{
 "messages":[
  {
   "role":"user",
   "content":"RAG چیست؟"
  }
 ]
}
```

---

نمونه پاسخ:

```json
{
 "message":
 "RAG یک معماری هوش مصنوعی برای ترکیب جستجو و تولید پاسخ است."
}
```

---

# ✅ ویژگی‌های سیستم

- اجرای کاملاً Local
- بدون نیاز به API خارجی
- پشتیبانی از زبان فارسی
- قابلیت استفاده از دانش اختصاصی
- جستجوی معنایی با Vector Database
- قابل توسعه برای پروژه‌های بزرگ‌تر

---



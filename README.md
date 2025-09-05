# URL Shortener API

Сервис доступен по ссылке: [foodsoul.maxray.pro](https://foodsoul.maxray.pro)

## Описание  
Этот сервис предоставляет API для управления короткими ссылками.  
Авторизация осуществляется через **API ключ**, который доступен на главной странице после входа на сайт.  

Все запросы к API должны содержать заголовок авторизации:  

```
Authorization: Bearer <API_KEY>
```

## Методы API  

### 1. Получить список всех ссылок пользователя  
**Запрос:**  
```
GET /api/v1/url/all
```

**Ответ (200):**  
```json
[
  {
    "id": 1,
    "short_url": "https://example.com/abc123",
    "long_url": "https://long-domain.com/page",
    "created_at": "2025-09-05 15:25:59"
  },
  {
    "id": 2,
    "short_url": "https://example.com/xyz789",
    "long_url": "https://another.com/test",
    "created_at": "2025-09-05 15:26:38"
  }
]
```

---

### 2. Создать короткую ссылку
**Запрос:**
```
POST /api/v1/url/create
Content-Type: application/x-www-form-urlencoded
```

**Параметры:**
- `long_url` — оригинальная длинная ссылка (обязательный параметр).

**Пример:**
```bash
curl -X POST https://example.com/api/v1/url/create \
  -H "Authorization: Bearer <API_KEY>" \
  -d "url=https://google.com"
```

**Ответ (200):**
```json
{
  "id": 3,
  "short_url": "https://example.com/HKSMSpPfHaJ",
  "long_url": "https://google.com",
  "created_at": "2025-09-05 16:10:12"
}
```

---

### 3. Удалить короткую ссылку
**Запрос:**
```
DELETE /api/v1/url/{id}
```

**Пример:**
```bash
curl -X DELETE https://example.com/api/v1/url/3 \
  -H "Authorization: Bearer <API_KEY>"
```

**Ответ (200):**
```json
{
  "success": true
}
```

**Ответ (404):**
```json
{
  "error": "URL not found"
}
```

### 4. Получить длинную ссылку из короткой

Также доступна возможность использовать обычные короткие ссылки напрямую.  
Если добавить параметр `?json=1`, то вместо редиректа сервис вернёт JSON:

**Пример:**

```bash
GET https://example.com/HKSMSpPfHaJ?json=1
```

**Ответ:**
```json
{
  "short_url": "https://example.com/HKSMSpPfHaJ",
  "long_url": "https://google.com"
}
```

---

## Ошибки

- **401 Unauthorized** — отсутствует или неверный API ключ.
- **400 Bad Request** — ошибка валидации (например, не передан `url`).
- **404 Not Found** — ресурс не найден.
# Laravel Order Management API

Bu proje, turkuaz medya işe giriş için hazırlanan bir assessmenttır. API servislerin nasıl kullanılacağına dair örnek request body'leri aşağıdadır.

## Kurulum

1. Veritabanını yapılandırın ve migrasyonları çalıştırın:

    ```sh
    php artisan migrate
    ```

2. Dummy verileri ekleyin:

    ```sh
    php artisan db:seed
    ```

3. Sunucuyu başlatın:

    ```sh
    php artisan serve
    ```

## API Kullanımı

### Authentication

API isteklerinde headerda `Authorization` başlığı altında token göndermelisiniz (Authorization: Bearer <token>). Token alabilmek için öncelikle api/register endpoint ine istek atarak kayıt olmalısınız. Örnek istekler aşağıdaki gibidir:


### Kayıt Olma

**Endpoint**: `POST /api/register`

#### Request Body

```json
{
    "name" : "test kullanıcı",
    "email" : "kayayilmaz9@gmail.com",
    "password" : "12345678"
}

```

#### Response Body

```json
{
    "token": "dMPjP5XTVx3DkW1JuzidIYTxB7T5RxsTWWelsm4cFEvgZmDw8HEYePbJHkNe"
}
```

### Zaten kayıt olup api/logout servisini çalıştırdıysanız, tekrar giriş yapmak için

**Endpoint**: `POST /api/login`

#### Request Body

```json
{
    "email" : "kayayilmaz9@gmail.com",
    "password" : "12345678"
}

```

#### Response Body

```json
{
    "token": "dMPjP5XTVx3DkW1JuzidIYTxB7T5RxsTWWelsm4cFEvgZmDw8HEYePbJHkNe"
}
```

### Sipariş Oluşturma

**Endpoint**: `POST /api/orders`

#### Request Body

```json
{
    "products": [
        {
            "id": 1,
            "quantity": 2
        },
        {
            "id": 2,
            "quantity": 1
        },
         {
            "id": 3,
            "quantity": 1
        }
    ]
}
```

### Sipariş Detayı Görüntüleme

**Endpoint**: `GET /api/orders/{order_code}`

### Yeni Kampanya Oluşturma

**Endpoint**: `POST /api/campaigns`

```json
{
    "name": "Yaz İndirimi",
    "description": "Yaz İndirimleri Geldi",
    "discount_type": "total_amount_percentage",
    "discount_value": 15,
    "conditions": {
        "min_total": 200
    }
}
```

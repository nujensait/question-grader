# VAR-6_f2: Реализация LLM-грейдера для экзаменационных вопросов

## Задача
Реализовать Artisan-команду `grade:questions` для автоматической оценки качества 60 экзаменационных вопросов с использованием Deepseek LLM по 4 критериям с выводом оценок от 1 до 10 в файл `grades.json`.

## История разработки

### 2026-06-17 15:40 - Реализация всех компонентов системы

**Что сделано:**

1. **Создана архитектура на основе SOLID принципов:**
   - DTO классы: `QuestionDTO`, `GradeResultDTO`
   - Интерфейсы: `LlmClientInterface`, `GradeCacheInterface`
   - Сервисы: `QuestionGraderService`, `PromptBuilder`, `RetryHandler`
   - Клиент: `DeepseekClient` для работы с Deepseek API
   - Кеширование: `FileCacheRepository` (JSON в storage/app/grades_cache.json)

2. **Реализованы ключевые функции:**
   - Автоматическая оценка вопросов по 4 критериям:
     * Content Validity (содержательная корректность)
     * Construct Clarity (четкость конструкции)
     * Distractor Quality (качество дистракторов)
     * Explanation Usefulness (полезность объяснения)
   - Механизм ретраев с экспоненциальной задержкой (1, 2, 4 сек)
   - Кеширование оценок для предотвращения повторных запросов
   - Обработка ошибок с дефолтной оценкой 1 при неудаче

3. **Создана Artisan команда:**
   - `php artisan grade:questions`
   - Читает `questions/questions.json`
   - Выводит результаты в `grades.json`
   - Минимальный вывод в консоль

4. **Настроена конфигурация:**
   - Файл `config/llm.php` с настройками Deepseek
   - Переменные окружения в `.env.example`:
     * DEEPSEEK_API_KEY
     * DEEPSEEK_API_URL
     * DEEPSEEK_MODEL
   - Биндинг интерфейсов в `AppServiceProvider`

**Структура файлов:**
```
app/
├── Console/Commands/
│   └── GradeQuestionsCommand.php
├── DTO/
│   ├── QuestionDTO.php
│   └── GradeResultDTO.php
├── Services/
│   ├── Llm/
│   │   ├── LlmClientInterface.php
│   │   ├── DeepseekClient.php
│   │   ├── GradeCacheInterface.php
│   │   └── FileCacheRepository.php
│   └── QuestionGrader/
│       ├── QuestionGraderService.php
│       ├── PromptBuilder.php
│       └── RetryHandler.php
config/
└── llm.php
```

**Почему так реализовано:**
- Использование интерфейсов позволяет легко заменить Deepseek на другой LLM провайдер
- Разделение ответственности между классами упрощает тестирование и поддержку
- Кеширование экономит API-запросы и деньги при повторных запусках
- Механизм ретраев обеспечивает надежность при временных сбоях API
- Дефолтная оценка 1 гарантирует, что все 60 вопросов получат оценку

## Инструкции по тестированию

### Подготовка:
1. Добавить в файл `.env` (вручную):
```bash
DEEPSEEK_API_KEY=your_api_key_here
DEEPSEEK_API_URL=https://api.deepseek.com/v1/chat/completions
DEEPSEEK_MODEL=deepseek-chat
```

2. Убедиться, что файл `questions/questions.json` существует и содержит 60 вопросов

### Тестирование в консоли:
```bash
# Запуск команды оценки
wsl php artisan grade:questions

# Проверка результата
wsl cat grades.json

# Проверка кеша
wsl cat storage/app/grades_cache.json

# Просмотр логов
wsl tail -f storage/logs/laravel.log
```

### Проверка результатов:
1. Файл `grades.json` должен содержать объект с ключом `results`
2. В `results` должно быть ровно 60 записей (по количеству вопросов)
3. Все оценки должны быть целыми числами от 1 до 10
4. При повторном запуске команды оценки берутся из кеша (быстрое выполнение)
5. Логи должны показывать процесс оценки и использование кеша

### Тестирование обработки ошибок:
```bash
# Тест с неверным API ключом (должны быть ретраи и оценка 1)
wsl php artisan grade:questions

# Проверка логов на наличие ретраев
wsl grep "Retry attempt" storage/logs/laravel.log
```

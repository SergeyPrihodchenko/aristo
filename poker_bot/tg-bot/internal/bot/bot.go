package bot

import (
	"log"
	handler "peletonbot/internal/http"
	"peletonbot/internal/models"

	tgbotapi "github.com/bs9/telegram-bot-api/v5"
)

const AUTH_URL_MINI_APP = "https://peleton-tg-group.ru/tg-app-auth-link"

type CreatedUsers map[int64]*models.User

func StartBot(token string, handler handler.HandlerInterface) {
    bot, err := tgbotapi.NewBotAPI(token)
    if err != nil {
        log.Panic(err)
    }

    // Удаляем webhook, чтобы можно было использовать long polling
    bot.Request(tgbotapi.DeleteWebhookConfig{})
    log.Printf("🤖 Бот запущен как %s", bot.Self.UserName)

    u := tgbotapi.NewUpdate(0)
    u.Timeout = 60
    updates := bot.GetUpdatesChan(u)

    for update := range updates {
        if update.Message != nil {
            user := models.User{
                TelegramID: update.Message.From.ID,
                ChatID:     update.Message.Chat.ID,
                Username:   update.Message.From.UserName,
                FirstName:  update.Message.From.FirstName,
                LastName:   update.Message.From.LastName,
            }

            switch update.Message.Text {
            case "/start":
                message := tgbotapi.NewMessage(update.Message.Chat.ID, "Привет! Я бот для управления твоими уведомлениями и доступа к мини-приложению. Используй /auth_link для получения ссылки на мини-приложение и /add_listner для получения уведомлений.")
                go func() {
                    err := handler.SendUserData(user)
                    if err != nil {
                        log.Printf("Ошибка при отправке данных пользователя: %v", err)
                    }
                }()
                bot.Send(message)
            default:
                message := tgbotapi.NewMessage(update.Message.Chat.ID, "Неизвестная команда.")
                bot.Send(message)
            }
        }
    }
}
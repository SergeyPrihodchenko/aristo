package bot

import (
	"log"
	"peletonbot/internal/models"
	"peletonbot/internal/web"

	tgbotapi "github.com/bs9/telegram-bot-api/v5"
)

const AUTH_URL_MINI_APP = "https://peleton-tg-group.ru/tg-app-auth-link"

type CreatedUsers map[int64]*models.User

func StartBot(token string, handler web.HandlerInterface) {
    bot, err := tgbotapi.NewBotAPI(token)
    if err != nil {
        log.Panic(err)
    }

    createdUsers := make(CreatedUsers)

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
                if _, ok := createdUsers[user.TelegramID]; !ok {
                    createdUsers[user.TelegramID] = &user

                    var avatarURL string
                    photos, err := bot.GetUserProfilePhotos(tgbotapi.UserProfilePhotosConfig{
                        UserID: update.Message.From.ID,
                        Limit:  1,
                    })

                    if err != nil {
                        log.Printf("Ошибка при получении фото профиля: %v", err)
                    }

                    if photos.TotalCount > 0 && len(photos.Photos) > 0 {

                        // Берём самое большое фото первой аватарки
                        biggestPhoto := photos.Photos[0][len(photos.Photos[0])-1]

                        file, err := bot.GetFile(tgbotapi.FileConfig{
                            FileID: biggestPhoto.FileID,
                        })

                        if err != nil {
                            log.Printf("Ошибка получения файла: %v", err)
                        } else {
                            avatarURL = file.Link(token)
                        }
                    }

                    go func() {
                        err := handler.SendUserData(user, avatarURL)
                        if err != nil {
                            log.Printf("Ошибка при отправке данных пользователя: %v", err)
                        }
                    }()
                    }
                bot.Send(message)
            default:
                message := tgbotapi.NewMessage(update.Message.Chat.ID, "Неизвестная команда.")
                bot.Send(message)
            }
        }
    }
}
package buttons

import tgbotapi "github.com/bs9/telegram-bot-api/v5"

func CallBack(update *tgbotapi.Update, bot *tgbotapi.BotAPI) {

	cb := update.CallbackQuery
	var response string
	switch cb.Data {
		case "auth_pass":
			response := "Нажмите кнопку ниже:"

			msg := tgbotapi.NewMessage(cb.Message.Chat.ID, response)

			// Кнопка-ссылка (InlineKeyboardButton)
			button := tgbotapi.NewInlineKeyboardButtonURL("Перейти по ссылке", "https://example.com")
			keyboard := tgbotapi.NewInlineKeyboardMarkup(
				tgbotapi.NewInlineKeyboardRow(button),
			)

			msg.ReplyMarkup = keyboard
			bot.Send(msg)
			return

		case "callback_2":
			response = "Ты выбрал 📦 Callback 2"
		default:
			response = "Неизвестная кнопка"
	}
	// Ответ пользователю в чате
	msg := tgbotapi.NewMessage(cb.Message.Chat.ID, response)
	bot.Send(msg)
	// Ответ самому Telegram (чтобы убрать “часики”)
	bot.Request(tgbotapi.NewCallback(cb.ID, "Принято 👍"))
}
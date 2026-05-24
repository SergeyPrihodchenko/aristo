package buttons

import tgbotapi "github.com/bs9/telegram-bot-api/v5"


func GetInlineKeyboard() tgbotapi.InlineKeyboardMarkup {
	return tgbotapi.NewInlineKeyboardMarkup(
					tgbotapi.NewInlineKeyboardRow(
						tgbotapi.NewInlineKeyboardButtonData("Авторизоваться", "auth_pass"),
						tgbotapi.NewInlineKeyboardButtonData("📦 Callback 2", "callback_2"),
					),
					tgbotapi.NewInlineKeyboardRow(
						tgbotapi.NewInlineKeyboardButtonURL("🌐 Перейти на сайт", "https://golang.org"),
						tgbotapi.NewInlineKeyboardButtonSwitch("🔍 Inline-режим", "demo_query"),
					),
				)
}

func GetReplyKeyboard() tgbotapi.ReplyKeyboardMarkup {
	return tgbotapi.NewReplyKeyboard(
					tgbotapi.NewKeyboardButtonRow(
						tgbotapi.KeyboardButton{
							Text:            "📱 Отправить контакт",
							RequestContact:  true,
						},
						tgbotapi.KeyboardButton{
							Text:             "📍 Отправить локацию",
							RequestLocation:  true,
						},
					),
					tgbotapi.NewKeyboardButtonRow(
						tgbotapi.NewKeyboardButton("меню"),
						tgbotapi.NewKeyboardButton("❌ Убрать клавиатуру"),
					),
				)
}

func GetMiniAppButton(authLink string) tgbotapi.InlineKeyboardMarkup {
    webApp := tgbotapi.WebAppInfo{
        URL: authLink,
    }

    btn := tgbotapi.NewInlineKeyboardButtonWebApp("Open Mini App", webApp)
    return tgbotapi.NewInlineKeyboardMarkup(
        tgbotapi.NewInlineKeyboardRow(btn),
    )
}

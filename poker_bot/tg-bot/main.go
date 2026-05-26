package main

import (
	"log"
	"os"

	"peletonbot/internal/bot"
	"peletonbot/internal/web"

	"github.com/joho/godotenv"
)

type HttpConfig struct {
	MiniAppURL string
}

func main() {
	err := godotenv.Load()
	if err != nil {
		log.Fatal("Ошибка загрузки .env файла")
	}
	var httpConfig HttpConfig
	httpConfig.MiniAppURL = os.Getenv("MINI_APP_URL")
	var token string = os.Getenv("TELEGRAM_TOKEN")

	handler := web.NewHandler(web.Config{MiniAppUrl: httpConfig.MiniAppURL})
	bot.StartBot(token, handler)
}

package web

import (
	"bytes"
	"encoding/json"
	"fmt"
	"net/http"
	"peletonbot/internal/models"
)

type HandlerInterface interface {
	SendUserData(user models.User, avatarURL string) error
	CreateUser(user models.User) (string, error)
}

type Config struct {
	MiniAppUrl string
}

type Handler struct {
	url string
}

type RequestUserData struct {
	TelegramID int64  `json:"telegram_id"`
	ChatID     int64  `json:"chat_id"`
	Username   string `json:"username"`
	FirstName  string `json:"first_name"`
	LastName   string `json:"last_name"`
	AvatarURL  string `json:"avatar_url"`
}

func NewHandler(config Config) *Handler {
	url := config.MiniAppUrl
	return &Handler{url: url}
}

func (h Handler) SendUserData(user models.User, avatarURL string) error {
	requestData := RequestUserData{
		TelegramID: user.TelegramID,
		ChatID:     user.ChatID,
		Username:   user.Username,
		FirstName:  user.FirstName,
		LastName:   user.LastName,
		AvatarURL: avatarURL,
	}
	
	reqBody, err := json.Marshal(requestData)
	if err != nil {
		return fmt.Errorf("ошибка при маршалинге данных: %v", err)
	}
	
	resp, err := http.Post(h.url + "/api/user_data", "application/json", bytes.NewBuffer(reqBody))
	if err != nil {
		return fmt.Errorf("ошибка при отправке данных: %v", err)
	}
	defer resp.Body.Close()

	if resp.StatusCode != http.StatusOK {
		return fmt.Errorf("неожиданный статус ответа: %v", resp.Status)
	}

	return nil
}

func (h Handler) CreateUser(user models.User) (string, error) {
	requestData := RequestUserData{
		TelegramID: user.TelegramID,
		ChatID:     user.ChatID,
		Username:   user.Username,
		FirstName:  user.FirstName,
		LastName:   user.LastName,
	}
	
	reqBody, err := json.Marshal(requestData)
	if err != nil {
		return "", fmt.Errorf("ошибка при маршалинге данных: %v", err)
	}
	
	resp, err := http.Post(h.url, "application/json", bytes.NewBuffer(reqBody))
	if err != nil {
		return "", fmt.Errorf("ошибка при отправке данных: %v", err)
	}
	defer resp.Body.Close()

	if resp.StatusCode != http.StatusOK {
		return "", fmt.Errorf("неожиданный статус ответа: %v", resp.Status)
	}

	var response struct {
		Token string `json:"token"`
	}
	if err := json.NewDecoder(resp.Body).Decode(&response); err != nil {
		return "", fmt.Errorf("ошибка при декодировании ответа: %v", err)
	}

	return response.Token, nil
}
package links

import "fmt"

const baseURL = "https://example.com"

func GenerateLink(userID int64) string {
	// Пример генерации ссылки с параметром userID
	return fmt.Sprintf("%s?user_id=%d", baseURL, userID)
}